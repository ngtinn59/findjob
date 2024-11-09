<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("email");
            $table->string("phone");
            $table->enum('status', [
                'pending',      // Đang chờ
                'contacted',    // Đã liên hệ
                'test_round',   // Vòng test
                'interview',    // Vòng phỏng vấn
                'hired',        // Trúng tuyển
                'not_selected'  // Không đúng tuyển
            ])->default('pending');
            $table->softDeletes();  // Thêm cột soft delete

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_users');
    }
};
