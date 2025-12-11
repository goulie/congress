<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        // On récupère les factures en attente depuis plus de 48h
        $pendingInvoices = Invoice::where('status', Invoice::PAYMENT_STATUS_UNPAID)
            ->where('deadline', '<=', Carbon::now())
            ->get();

        if ($pendingInvoices->isEmpty()) {
            $this->info('Aucune facture pending trouvée.');
            Log::info('Aucune facture pending trouvée. Pour mise à jour');
            return;
        }

        foreach ($pendingInvoices as $invoice) {

            $participant = $invoice->participant;

            /** 1️⃣ On expire l’ancienne facture */
            $invoice->status = Invoice::PAYMENT_STATUS_EXPIRED;
            $invoice->save();

            /** 2️⃣ On crée une nouvelle facture (ex : même montant, même participant) */
            $newInvoice = Invoice::create([
                'participant_id' => $participant->id,
                'amount'         => $invoice->amount,
                'currency'       => $invoice->currency,
                'status'         => 'pending',
                'description'    => $invoice->description . ' (renouvelé)',
                'due_date'       => Carbon::now()->addDays(2),
            ]);

            /** 3️⃣ On informe le participant par email */

            // Email 1 : l’ancienne facture expirée
            Mail::to($participant->email)->send(new PendingInvoiceExpired($invoice));

            // Email 2 : la nouvelle facture émise
            Mail::to($participant->email)->send(new NewInvoiceCreated($newInvoice));

            $this->info("Facture #{$invoice->id} expirée et nouvelle facture #{$newInvoice->id} créée.");
        }
    }
}
