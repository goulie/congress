<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicesReminder;
use Illuminate\Support\Facades\Log;

class PaymentReminderService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendReminders(int $congresId): int
    {
        $count = 0;

        $invoices = Invoice::unpaid()
            ->where('congres_id', $congresId)
            ->whereHas('participant', fn($q) => $q->whereNotNull('email'))
            ->with('participant')
            ->get();

        foreach ($invoices as $invoice) {

            // Sécurité anti-spam (optionnel)
            if ($invoice->reminder_sent_at && $invoice->reminder_sent_at->diffInDays(now()) < 3) {
                continue;
            }

            $this->emailService->sendPaymentReminder($invoice);

            InvoicesReminder::create([
                'reminder_sent_at' => now(),
                'invoice_id'=> $invoice->id,
                'reminder_type'=> 'Payment Reminder',
            ]);

            $count++;
        }

        return $count;
    }
}
