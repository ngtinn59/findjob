<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Đảm bảo bạn sử dụng đúng model User
use Carbon\Carbon;

class DeleteUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users who have not verified their email for a certain period';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Thời gian chưa xác nhận email (Ví dụ: 1 phút)
        $minutesLimit = 1;

        // Lấy tất cả người dùng chưa xác nhận email và tạo trước $minutesLimit phút
        $users = User::whereNull('email_verified_at')
            ->where('created_at', '<', Carbon::now()->subMinutes($minutesLimit))
            ->get();

        // Kiểm tra nếu có người dùng
        if ($users->isEmpty()) {
            $this->info('No unverified users found to delete.');
            return;
        }

        // Xoá người dùng chưa xác nhận email
        foreach ($users as $user) {
            $user->delete();
            $this->info('Deleted user: ' . $user->email);
        }

        $this->info('All unverified users have been deleted.');
    }
}
