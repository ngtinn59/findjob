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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->unsignedBigInteger('desired_level_id')->nullable();// Khóa ngoại tới bảng professions
            $table->unsignedBigInteger('employment_type_id')->nullable(); // Khóa ngoại tới bảng employment_types
            $table->unsignedBigInteger('experience_level_id')->nullable(); // Khóa ngoại tới bảng experience_levels
            $table->unsignedBigInteger('education_level_id')->nullable(); // Khóa ngoại tới bảng education_levels
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();

            $table->text('title')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('salary_from')->nullable();
            $table->integer('salary_to')->nullable();
            $table->text('work_address')->nullable();
            $table->date('last_date')->nullable();
            $table->text('description')->nullable();
            $table->text('skill_experience')->nullable();
            $table->text('benefits')->nullable();
            $table->text('work_location')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('contact_name')->nullable();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->unsignedBigInteger('views')->default(0); // Thêm cột views và thiết lập giá trị mặc định là 0

            $table->integer('status')->nullable();
            $table->integer('featured')->nullable();

            // Foreign key constraints
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('set null');
            $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('set null');
            $table->foreign('experience_level_id')->references('id')->on('experience_levels')->onDelete('set null');
            $table->foreign('education_level_id')->references('id')->on('education_levels')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('desired_level_id')->references('id')->on('desired_levels')->onDelete('set null');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
