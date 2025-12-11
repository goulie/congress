<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorieRegistrant;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminRegistrationController extends Controller
{
    public function home()
    {
        $oldInvoice = Invoice::latest()->first();
        $oldInvoiceItems = InvoiceItem::where('invoice_id', $oldInvoice->id);
        $gala = $oldInvoiceItems->where('description_fr', 'like', '%gala%');

        try {
            if (!$oldInvoice || $oldInvoice->status !== Invoice::PAYMENT_STATUS_UNPAID) {
                return null;
            }

            $congres = Congress::latest()->first();
            if (!$congres) {
                Log::warning('Aucun congrès trouvé pour mise à jour des tarifs.');
                return null;
            }

            // Charger tous les tarifs du congrès (utiles pour détection par mot-clé)
            $dinner      = CategorieRegistrant::DinnerforCongress($congres->id);
            $tours       = CategorieRegistrant::ToursforCongress($congres->id);
            $deleguate   = CategorieRegistrant::deleguateForCongress($congres->id);
            $student_ywp = CategorieRegistrant::studentForCongress($congres->id);
            $non_member  = CategorieRegistrant::NonMemberPriceforCongress($congres->id);
            $passDeleguate = CategorieRegistrant::PassDeleguateforCongress($congres->id);

            return DB::transaction(function () use ($oldInvoice, $congres, $dinner, $tours, $deleguate, $student_ywp, $non_member, $passDeleguate) {

                // Récupérer participant
                $participant = $oldInvoice->participant;
                if (!$participant) {
                    Log::warning("Invoice #{$oldInvoice->id} sans participant lié.");
                    return null;
                }

                // Expire l'ancienne facture
                $oldInvoice->update([
                    'status' => Invoice::PAYMENT_STATUS_EXPIRED,
                    'expired_at' => Carbon::now(), // si tu as ce champ
                ]);

                // Création nouvelle facture
                $invoiceNumber = 'INV' . Carbon::parse($congres->end_date)->year . '-' . strtoupper(Str::random(6));
                $userId = Auth::id() ?: ($oldInvoice->user_id ?? null);

                $newInvoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id'        => $oldInvoice->user_id,
                    'participant_id' => $oldInvoice->participant_id,
                    'currency'       => $congres->currency ?? ($oldInvoice->currency ?? 'FCFA'),
                    'status'         => Invoice::PAYMENT_STATUS_UNPAID,
                    'invoice_date'   => Carbon::now(),
                    'congres_id'     => $congres->id,
                ]);

                // Parcours et recréation des items avec tarification mise à jour
                $oldItems = $oldInvoice->items()->get();

                foreach ($oldItems as $oldItem) {

                    // Valeurs par défaut prises depuis l'item ancien
                    $descriptionFr = $oldItem->description_fr ?? '';
                    $descriptionEn = $oldItem->description_en ?? ($oldItem->description_fr ?? '');
                    $currency      = $oldItem->currency ?? $newInvoice->currency;
                    $tarifId       = $oldItem->tarif_id ?? null;
                    $newPrice      = $oldItem->price;

                    // 1) Si tarif_id présent et valide -> appliquer montant courant du tarif
                    if (!empty($tarifId) && $tarifId != 0) {
                        $tarif = CategorieRegistrant::find($tarifId);
                        if ($tarif && isset($tarif->montant) && $tarif->montant > 0) {
                            $newPrice = $tarif->montant;
                            // Mettre à jour description si disponible
                            if (!empty($tarif->libelle_fr)) {
                                $descriptionFr = $tarif->libelle_fr;
                            }
                            if (!empty($tarif->libelle_en)) {
                                $descriptionEn = $tarif->libelle_en;
                            }
                        }
                    } else {
                        // 2) Détection par mot clé dans description_fr (fallback)
                        $descLower = mb_strtolower($descriptionFr);

                        if (mb_stripos($descLower, 'gala') !== false || mb_stripos($descLower, 'diner') !== false) {
                            if ($dinner && isset($dinner->montant) && $dinner->montant > 0) {
                                $newPrice = $dinner->montant;
                                $tarifId = $dinner->tarif_id ?? $tarifId;
                                $descriptionFr = $dinner->libelle_fr ?? $descriptionFr;
                            }
                        } elseif (mb_stripos($descLower, 'visite') !== false || mb_stripos($descLower, 'tour') !== false) {
                            if ($tours && isset($tours->montant) && $tours->montant > 0) {
                                $newPrice = $tours->montant;
                                $tarifId = $tours->tarif_id ?? $tarifId;
                                $descriptionFr = $tours->libelle_fr ?? $descriptionFr;
                            }
                        } elseif (
                            mb_stripos($descLower, 'pass délégué') !== false
                            || mb_stripos($descLower, 'pass delegue') !== false
                            || mb_stripos($descLower, 'pass delegate') !== false
                        ) {
                            if ($passDeleguate && isset($passDeleguate->montant) && $passDeleguate->montant > 0) {
                                $newPrice = $passDeleguate->montant;
                                $tarifId = $passDeleguate->tarif_id ?? $tarifId;
                            }
                        } elseif (
                            mb_stripos($descLower, 'délégué') !== false
                            || mb_stripos($descLower, 'delegue') !== false
                            || mb_stripos($descLower, 'delegate') !== false
                        ) {
                            // Delegate: si participant non membre, appliquer non_member sinon deleguate
                            if ($participant->membre_aae === 'non' || $participant->membre_aae === 'Non' || $participant->isMember === false) {
                                if ($non_member && isset($non_member->montant) && $non_member->montant > 0) {
                                    $newPrice = $non_member->montant;
                                    $tarifId = $non_member->tarif_id ?? $tarifId;
                                }
                            } else {
                                if ($deleguate && isset($deleguate->montant) && $deleguate->montant > 0) {
                                    $newPrice = $deleguate->montant;
                                    $tarifId = $deleguate->tarif_id ?? $tarifId;
                                }
                            }
                        } elseif (
                            mb_stripos($descLower, 'étudiant') !== false
                            || mb_stripos($descLower, 'etudiant') !== false
                            || mb_stripos($descLower, 'ywp') !== false
                        ) {
                            if ($student_ywp && isset($student_ywp->montant) && $student_ywp->montant > 0) {
                                $newPrice = $student_ywp->montant;
                                $tarifId = $student_ywp->tarif_id ?? $tarifId;
                            }
                        }
                        // sinon on garde le prix existant
                    }

                    // Créer le nouvel item sur la nouvelle facture
                    $newItem = $newInvoice->items()->create([
                        'description_fr' => $descriptionFr,
                        'description_en' => $descriptionEn,
                        'price'          => $newPrice,
                        'currency'       => $currency,
                        'tarif_id'       => $tarifId,
                    ]);
                }

                // Recalculer total
                $total = $newInvoice->items()->sum('price');
                $newInvoice->update(['total_amount' => $total]);

                // Envoi des emails : ancienne facture expirée + nouvelle facture
                try {
                    // Email pour ancienne facture (optionnel)
                    //$this->emailService->SendInvoiceEmail($oldInvoice);
                } catch (\Exception $e) {
                    Log::warning("Erreur envoi email ancienne facture #{$oldInvoice->id}: " . $e->getMessage());
                }

                try {
                    //$this->emailService->SendInvoiceEmail($newInvoice);
                } catch (\Exception $e) {
                    Log::warning("Erreur envoi email nouvelle facture #{$newInvoice->id}: " . $e->getMessage());
                }

                return $newInvoice->load('items');
            });
        } catch (\Exception $ex) {
            Log::error("ExpiredInvoiceService.processPendingInvoice error: " . $ex->getMessage());
            return $ex->getMessage();
        }
    }
}
