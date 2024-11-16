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
        $schedule->command('users:delete-unverified')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/schedule.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Đảm bảo load tất cả các command đã đăng ký
        $this->load(__DIR__.'/Commands');

        // Đăng ký file console.php nếu cần
        require base_path('routes/console.php');
    }

    // Đăng ký các command của ứng dụng
    protected $commands = [
        \App\Console\Commands\DeleteUnverifiedUsers::class,
    ];
}
