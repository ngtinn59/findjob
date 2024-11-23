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
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Tạo khóa chính tự động tăng
            $table->string('title'); // Tiêu đề bài viết
            $table->text('body'); // Nội dung bài viết
            $table->foreignId('author_id')->constrained('users'); // Khóa ngoại liên kết đến bảng users
            $table->timestamps(); // Thêm cột created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
