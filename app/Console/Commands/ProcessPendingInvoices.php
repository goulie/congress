<?php

namespace App\Console\Commands;

use App\Mail\InvoiceUpdatedMail;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicesReminder;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProcessPendingInvoices extends Command
{
    protected $signature = 'invoices:process-pending';
    protected $description = 'Expire les factures en attente, crée une nouvelle facture et informe le participant';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $congres = Congress::latest()->first();

        if (!$congres) {
            $this->error('Aucun congrès trouvé pour mise à jour des tarifs.');
            Log::warning('Aucun congrès trouvé pour mise à jour des tarifs.');
            return 1;
        }

        $totalProcessed = 0;
        $totalErrors = 0;

        // Utilisation de chunk pour traiter par lots de 50 (ajustable selon vos besoins)
        Invoice::where('status', Invoice::PAYMENT_STATUS_UNPAID)
            ->where('deadline', '<=', Carbon::now())
            ->chunk(50, function ($pendingInvoices) use ($congres, &$totalProcessed, &$totalErrors) {

                foreach ($pendingInvoices as $oldInvoice) {
                    try {
                        $this->processSingleInvoice($oldInvoice, $congres);
                        $totalProcessed++;

                        // Afficher une progression
                        if ($totalProcessed % 10 === 0) {
                            $this->info("{$totalProcessed} factures traitées...");
                        }
                    } catch (\Exception $ex) {
                        $totalErrors++;
                        Log::error("Erreur lors du traitement de la facture #{$oldInvoice->id}: " . $ex->getMessage());
                        $this->error("Erreur avec la facture #{$oldInvoice->id}: " . $ex->getMessage());
                    }
                }
            });

        if ($totalProcessed === 0) {
            $this->info('Aucune facture pending trouvée à traiter.');
            Log::info('Aucune facture pending trouvée. Pour mise à jour');
        } else {
            $this->info("Traitement terminé : {$totalProcessed} factures traitées, {$totalErrors} erreurs.");
            Log::info("ProcessPendingInvoices terminé : {$totalProcessed} factures traitées, {$totalErrors} erreurs.");
        }

        return 0;
    }

    /**
     * Traite une facture individuelle
     *
     * @param Invoice $oldInvoice
     * @param Congress $congres
     * @return mixed
     */
    private function processSingleInvoice(Invoice $oldInvoice, Congress $congres)
    {
        return DB::transaction(function () use ($oldInvoice, $congres) {
            // Expire l'ancienne facture
            $oldInvoice->update([
                'status' => Invoice::PAYMENT_STATUS_EXPIRED,
                'expired_at' => Carbon::now(),
            ]);

            // Création nouvelle facture
            $invoiceNumber = 'INV-ICE' . Carbon::parse($congres->end_date)->year . '-' . strtoupper(Str::random(6));

            $newInvoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id'        => $oldInvoice->user_id,
                'participant_id' => $oldInvoice->participant_id,
                'currency'       => $congres->currency ?? ($oldInvoice->currency ?? 'FCFA'),
                'status'         => Invoice::PAYMENT_STATUS_UNPAID,
                'invoice_date'   => Carbon::now(),
                'congres_id'     => $congres->id,
                'deadline'       => Tarif::PeriodeActive($congres->id)->end_date
            ]);

            // Parcours et recréation des items avec tarification mise à jour
            $oldItems = $oldInvoice->items()->get();

            foreach ($oldItems as $oldItem) {
                $tarif = Tarif::TarifDuJour($oldItem->tarif->categorie_registrant_id, $congres->id);

                InvoiceItem::create([
                    'description_fr' => $oldItem->description_fr,
                    'price'          => $tarif->montant,
                    'tarif_id'       => $tarif->id,
                    'invoice_id'     => $newInvoice->id,
                    'currency'       => $oldItem->currency,
                ]);
            }

            // Recalculer total
            $total = $newInvoice->items()->sum('price');

            // Mise à jour de la facture
            $newInvoice->update([
                'total_amount' => $total,
                'period_id'    => $tarif->periode_id ?? null,
                'deadline'     => $tarif->periode->end_date ?? null,
            ]);

            InvoicesReminder::create([
                'reminder_sent_at' => now(),
                'invoice_id'=> $newInvoice->id,
                'reminder_type'=> 'New invoice Reminder' .$oldInvoice->invoice_number .'->'. $newInvoice->invoice_number,
            ]);
            
            // Envoi des emails
            $this->sendNotificationEmails($oldInvoice, $newInvoice);

            return $newInvoice->load('items');
        });
    }

    /**
     * Envoie les emails de notification
     *
     * @param Invoice $oldInvoice
     * @param Invoice $newInvoice
     * @return void
     */
    private function sendNotificationEmails(Invoice $oldInvoice, Invoice $newInvoice): void
    {
        // Envoi email pour la nouvelle facture
        try {
            if ($oldInvoice->participant && $oldInvoice->participant->email) {
                Mail::to($oldInvoice->participant->email)
                    ->queue(new InvoiceUpdatedMail($newInvoice));

                Log::info("Email envoyé pour la nouvelle facture #{$newInvoice->id} à {$oldInvoice->participant->email}");
            } else {
                Log::warning("Participant ou email non trouvé pour la facture #{$oldInvoice->id}");
            }
        } catch (\Exception $e) {
            Log::warning("Erreur envoi email nouvelle facture #{$newInvoice->id}: " . $e->getMessage());
        }

        // Optionnel : Envoi pour l'ancienne facture expirée
        try {
            // $this->emailService->sendExpiredInvoiceEmail($oldInvoice);
        } catch (\Exception $e) {
            Log::warning("Erreur envoi email facture expirée #{$oldInvoice->id}: " . $e->getMessage());
        }
    }
}
