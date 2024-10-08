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
        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->string('desired_position')->nullable(); // Vị trí mong muốn
            $table->string('desired_level')->nullable();     // Cấp bậc mong muốn
            $table->string('education_level')->nullable();  // Trình độ học vấn
            $table->integer('experience_years')->nullable(); // Kinh nghiệm (năm)

            $table->string('profession')->nullable();        // Nghề nghiệp
            $table->string('work_address')->nullable();      // Địa chỉ làm việc
            $table->decimal('expected_salary', 10, 2)->nullable(); // Mức lương mong muốn
            $table->string('work_location')->nullable();     // Nơi làm việc
            $table->string('employment_type')->nullable();   // Hình thức làm việc

            $table->unsignedBigInteger('profiles_id'); // Khóa ngoại liên kết đến bảng profiles
            $table->foreign('profiles_id')->references('id')->on('profiles')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
