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
        $schedule->command('invoices:process-pending')
            ->dailyAt('00:20')
            ->timezone('GMT') // Adaptez à votre fuseau horaire
            ->sendOutputTo(storage_path('logs/invoice-process.log')); // Optionnel : log dans un fichier
            //->emailOutputTo(['gouli1212@gmail.com','jgouli@afwasa.org']); // Optionnel : envoi des résultats par email
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
