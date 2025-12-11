<?php

namespace App\Services;

use App\Models\CategorieRegistrant;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpiredInvoiceService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }


    /**
     * Expire une facture pending et recrée une nouvelle facture
     * avec mise à jour des items selon les nouveaux tarifs.
     */
    public function processPendingInvoice(Invoice $oldInvoice)
    {
        try {
            if (!$oldInvoice || $oldInvoice->status !== Invoice::PAYMENT_STATUS_UNPAID) {
                return null;
            }

            $congres = Congress::latest()->first();
            if (!$congres) {
                Log::warning("Aucun congrès trouvé pour mise à jour facture.");
                return null;
            }

            // Charger les tarifs du congrès
            $tarifs = $this->loadTarifs($congres->id);

            return DB::transaction(function () use ($oldInvoice, $congres, $tarifs) {

                /** -------------------------------------
                 * 1️⃣ Expirer ancienne facture
                 * -------------------------------------*/
                $oldInvoice->update([
                    'status'     => Invoice::PAYMENT_STATUS_EXPIRED,
                    'expired_at' => Carbon::now(),
                ]);

                /** -------------------------------------
                 * 2️⃣ Créer une nouvelle facture
                 * -------------------------------------*/
                $newInvoice = $this->createNewInvoice($oldInvoice, $congres);

                /** -------------------------------------
                 * 3️⃣ Recréer les items avec nouveaux tarifs
                 * -------------------------------------*/
                $this->rebuildItems($oldInvoice, $newInvoice, $tarifs);

                /** -------------------------------------
                 * 4️⃣ Recalcul total
                 * -------------------------------------*/
                $newInvoice->update([
                    'total_amount' => $newInvoice->items()->sum('price')
                ]);

                /** -------------------------------------
                 * 5️⃣ Email (optionnel)
                 * -------------------------------------*/
                try {
                    //$this->emailService->SendInvoiceEmail($newInvoice);
                } catch (\Exception $e) {
                    Log::warning("Erreur envoi email facture #{$newInvoice->id}: " . $e->getMessage());
                }

                return $newInvoice->load('items');
            });
        } catch (\Exception $e) {

            Log::error("ExpiredInvoiceService error: " . $e->getMessage());
            return null;
        }
    }



    /**
     * Charge tous les tarifs d’un congrès.
     */
    private function loadTarifs($congresId)
    {
        return [
            'dinner'        => CategorieRegistrant::DinnerforCongress($congresId),
            'tours'         => CategorieRegistrant::ToursforCongress($congresId),
            'deleguate'     => CategorieRegistrant::deleguateForCongress($congresId),
            'student_ywp'   => CategorieRegistrant::studentForCongress($congresId),
            'non_member'    => CategorieRegistrant::NonMemberPriceforCongress($congresId),
            'passDeleguate' => CategorieRegistrant::PassDeleguateforCongress($congresId),
        ];
    }



    /**
     * Crée une nouvelle facture basée sur l’ancienne.
     */
    private function createNewInvoice(Invoice $oldInvoice, Congress $congres)
    {
        return Invoice::create([
            'invoice_number' => 'INV-ICE' . $congres->year . '-' . strtoupper(Str::random(6)),
            'user_id'        => $oldInvoice->user_id,
            'participant_id' => $oldInvoice->participant_id,
            'currency'       => $congres->currency ?? $oldInvoice->currency,
            'status'         => Invoice::PAYMENT_STATUS_UNPAID,
            'invoice_date'   => Carbon::now(),
            'congres_id'     => $congres->id,
        ]);
    }



    /**
     * Met à jour un item selon tarif_id ou mots-clés.
     */
    private function computeNewPrice($item, $participant, $tarifs)
    {
        $desc = mb_strtolower($item->description_fr ?? '');

        // 1️⃣ Si tarif_id existant, appliquer le montant du tarif
        if (!empty($item->tarif_id)) {
            $tarif = CategorieRegistrant::find($item->tarif_id);

            if ($tarif && $tarif->montant > 0) {
                return [
                    'price'       => $tarif->montant,
                    'tarif_id'    => $tarif->tarif_id,
                    'description' => $tarif->libelle_fr ?? $item->description_fr
                ];
            }
        }

        // 2️⃣ Sinon détecter par mots-clés
        if (str_contains($desc, 'gala')) {
            return $this->applyTarif($tarifs['dinner'], $item);
        }

        if (str_contains($desc, 'visite') || str_contains($desc, 'tour')) {
            return $this->applyTarif($tarifs['tours'], $item);
        }

        if (str_contains($desc, 'pass délégué') || str_contains($desc, 'pass delegate')) {
            return $this->applyTarif($tarifs['passDeleguate'], $item);
        }

        if (str_contains($desc, 'délégué') || str_contains($desc, 'delegate')) {

            if ($participant->membre_aae === 'non') {
                return $this->applyTarif($tarifs['non_member'], $item);
            }

            return $this->applyTarif($tarifs['deleguate'], $item);
        }

        if (str_contains($desc, 'étudiant') || str_contains($desc, 'ywp')) {
            return $this->applyTarif($tarifs['student_ywp'], $item);
        }

        //Sinon on retourne l’ancien prix
        return [
            'price'       => $item->price,
            'tarif_id'    => $item->tarif_id,
            'description' => $item->description_fr
        ];
    }



    /**
     * Applique un tarif sur un item si montant > 0.
     */
    private function applyTarif($tarif, $oldItem)
    {
        if (!$tarif || $tarif->montant <= 0) {
            return [
                'price'       => $oldItem->price,
                'tarif_id'    => $oldItem->tarif_id,
                'description' => $oldItem->description_fr
            ];
        }

        return [
            'price'       => $tarif->montant,
            'tarif_id'    => $tarif->tarif_id,
            'description' => $tarif->libelle_fr ?? $oldItem->description_fr
        ];
    }



    /**
     * Recrée les items dans la nouvelle facture.
     */
    private function rebuildItems(Invoice $oldInvoice, Invoice $newInvoice, $tarifs)
    {
        $participant = $oldInvoice->participant;

        foreach ($oldInvoice->items as $item) {

            $new = $this->computeNewPrice($item, $participant, $tarifs);

            $newInvoice->items()->create([
                'description_fr' => $new['description'],
                'price'          => $new['price'],
                'currency'       => $item->currency ?? $newInvoice->currency,
                'tarif_id'       => $new['tarif_id'],
            ]);
        }
    }
}
