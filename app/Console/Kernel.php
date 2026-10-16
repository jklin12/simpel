<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Backup database penuh seminggu sekali. Jumlah file yang disimpan diatur
        // BACKUP_KEEP (prod: 5 = ~1 bulan), file lebih lama dihapus otomatis.
        $schedule->command('db:backup --type=weekly')
            ->weeklyOn(0, '02:30') // Minggu 02:30
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
