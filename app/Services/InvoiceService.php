<?php

namespace App\Services;

use App\Models\Congress;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    protected $emailService;
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
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
                        'status'    => $data['status'] ?? 'pending',
                        'invoice_date' => Carbon::now(),
                    ]);
                    //envoie email de facture
                    $this->emailService->SendInvoiceEmail($invoice);
                    // Supprimer les anciens items avant de les recréer (si on remplace tout)
                    $invoice->items()->delete();
                } else {
                    // 🆕 Création d’une nouvelle facture
                    $invoiceNumber = 'INV' . Carbon::parse($congres->end_date)->year . '-' . strtoupper(Str::random(6));

                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'user_id'        => Auth::user()->user_id ?? Auth::user()->id,
                        'participant_id' => $data['participant_id'],
                        'currency'       => $congres->currency ?? 'FCFA',
                        'status'         => 'pending',
                        'invoice_date'   => Carbon::now(),
                        'congres_id'     => $congres->id,
                    ]);

                    //envoie email de facture
                    $this->emailService->SendInvoiceEmail($invoice);
                }

                // Ajout / mise à jour des items
                foreach ($items as $item) {
                    $invoice->items()->create([
                        'description_fr' => $item['description'],
                        'price'          => $item['price'],
                        'currency'       => $congres->currency ?? 'FCFA',
                    ]);
                }

                // Calcul total (si tu veux enregistrer le montant total dans la facture)
                $total = $invoice->items->sum('price');
                $invoice->update(['total_amount' => $total]);

                return $invoice->load('items');
            });
        } catch (\Exception $ex) {

            Log::error($ex->getMessage());
        }
    }
}
