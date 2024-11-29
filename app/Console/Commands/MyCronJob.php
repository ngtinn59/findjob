<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MyCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'my:cron-job'; // Tên lệnh để gọi trong Artisan.
    protected $description = 'My custom CronJob for Laravel API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Logic cho công việc
        \Log::info('CronJob is running successfully!');
    }
}
