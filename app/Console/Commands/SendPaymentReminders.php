<?php

namespace App\Console\Commands;

use App\Models\Congress;
use App\Services\PaymentReminderService;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:remind';
    protected $description = 'Send payment reminders to unpaid participants';

    public function handle(PaymentReminderService $service)
    {
        $congres = Congress::latest()->first();

        $count = $service->sendReminders($congres->id);

        $this->info("$count reminder(s) sent.");
    }
}
