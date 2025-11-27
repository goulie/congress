<?php

namespace App\Services;

use App\Models\Congress;
use App\Models\Invoice;
use App\Models\Periode;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Email;

class SingleRegistrantInvoice
{
    protected $EmailService;

    public function __construct(EmailService $EmailService)
    {
        $this->EmailService = $EmailService;
    }
    /**
     * Crée ou met à jour une facture et ses items.
     *
     * @param array $data Données principales de la facture
     * @param array $items Liste des items de la facture
     * @return Invoice
     */
    public function createOrUpdateInvoice(array $data, array $items)
    {
        try {

            $congres = Congress::latest()->first();

            return DB::transaction(function () use ($data, $items, $congres) {

                // Vérifie si une facture existe déjà pour ce participant
                $invoice = Invoice::where('participant_id', $data['participant_id'])->first();

                if ($invoice) {
                    // ✅ Mise à jour de la facture existante
                    $invoice->update([
                        'currency'  => $congres->currency ?? 'FCFA',
                        'status'    => $data['status'] ?? Invoice::PAYMENT_STATUS_UNPAID,
                        'invoice_date' => Carbon::now(),

                    ]);

                    // Supprimer les anciens items avant de les recréer (si on remplace tout)
                    $invoice->items()->delete();
                } else {
                    // 🆕 Création d’une nouvelle facture
                    $invoiceNumber = 'INV-ICE' . Carbon::parse($congres->end_date)->year . '-' . strtoupper(Str::random(6));

                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'user_id'        => Auth::id(),
                        'participant_id' => $data['participant_id'],
                        'currency'       => $congres->currency ?? 'FCFA',
                        'status'         => Invoice::PAYMENT_STATUS_UNPAID,
                        'invoice_date'   => Carbon::now(),
                        'congres_id'     => $congres->id,
                        'period_id' => Periode::PeriodeActive($congres->id, Carbon::now())->id,
                        'deadline' => Periode::PeriodeActive($congres->id, Carbon::now())->end_date
                    ]);
                }

                // Ajout / mise à jour des items
                foreach ($items as $item) {
                    $invoice->items()->create([
                        'description_fr' => $item['description'],
                        'price'          => $item['price'],
                        'tarif_id'          => $item['tarif_id'],
                        'currency'       => $congres->currency ?? 'FCFA',
                    ]);
                }

                // Calcul total (si tu veux enregistrer le montant total dans la facture)
                $total = $invoice->items->sum('price');
                $invoice->update(['total_amount' => $total]);

                return $invoice->load('items');
            });
        } catch (\Exception $ex) {

            Log::error('singleRegistrantInvoice error: ' . $ex->getMessage());
        }
    }
}
