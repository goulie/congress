<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Traitement des factures en attente (génération / recalcul)
        |--------------------------------------------------------------------------
        */
        $schedule->command('invoices:process-pending')
            ->dailyAt('00:20')
            ->timezone(config('app.timezone')) // évite les erreurs GMT
            ->withoutOverlapping()
            ->onOneServer() // important si plusieurs serveurs
            ->appendOutputTo(storage_path('logs/invoice-process.log'));

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Rappel de paiement (participants non payés)
        |--------------------------------------------------------------------------
        */
        $schedule->command('payments:remind')
            //->dailyAt('09:00')
            ->everyMinute()
            ->timezone(config('app.timezone'))
            ->withoutOverlapping()
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/payment-reminder.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
