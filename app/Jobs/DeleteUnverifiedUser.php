<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUnverifiedUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Kiểm tra xem người dùng đã xác minh email chưa
        if (!$this->user->hasVerifiedEmail()) {
            // Xóa người dùng nếu chưa xác minh
            $this->user->companies()->delete(); // Xóa dữ liệu công ty
            $this->user->delete();
        }
    }
}
