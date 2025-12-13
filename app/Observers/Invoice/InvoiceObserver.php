<?php

namespace App\Observers\Invoice;

use App\Models\CategorieRegistrant;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JourPassDelegue;
use App\Models\Participant;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    protected $participantService;
    /**
     * Handle the Invoice "created" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }

    protected function generateOrUpdateInvoice(Participant $participant)
    {
        $oldInvoice = Invoice::where([
            'participant_id' => $participant->id,
            'status' => Invoice::PAYMENT_STATUS_UNPAID
        ])->last();

        try {
            if (!$oldInvoice || $oldInvoice->status !== Invoice::PAYMENT_STATUS_UNPAID) {
                return null;
            }

            $congres = Congress::latest()->first();

            if (!$congres) {

                Log::warning('Aucun congrès trouvé pour mise à jour des tarifs.');
                return null;
            }

            return DB::transaction(function () use ($oldInvoice, $congres, $participant) {

                // Récupérer participant
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
                $invoiceNumber = 'INV-ICE' . Carbon::parse($congres->end_date)->year . '-' . strtoupper(Str::random(6));
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
                    // Créer le nouvel item sur la nouvelle facture

                    $item = new InvoiceItem();
                    $item->description_fr = $oldItems->description_fr;
                    $item->price = Tarif::TarifDuJour($oldItem->tarif->categorie_registrant_id, $congres->id)->montant;
                    $item->tarif_id = Tarif::TarifDuJour($oldItem->tarif->categorie_registrant_id, $congres->id)->id;
                    $item->invoice_id = $newInvoice->id;
                    $item->currency = $oldItems->currency;
                    $item->save();
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
