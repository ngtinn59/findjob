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
            $table->unsignedBigInteger('desired_level_id')->nullable();
            $table->unsignedBigInteger('education_level_id')->nullable();

            $table->unsignedBigInteger('profession_id')->nullable(); // Khóa ngoại tới bảng professions
            $table->unsignedBigInteger('employment_type_id')->nullable(); // Khóa ngoại tới bảng employment_types
            $table->integer('experience_years')->nullable(); // Kinh nghiệm (năm)
            $table->string('work_address')->nullable();      // Địa chỉ làm việc

            $table->integer('salary_from')->nullable();
            $table->integer('salary_to')->nullable();
            $table->string('file')->nullable();
            $table->string('status')->nullable();

            $table->unsignedBigInteger('profiles_id'); // Khóa ngoại liên kết đến bảng profiles
            $table->unsignedBigInteger('country_id')->nullable(); // Foreign key for countries
            $table->unsignedBigInteger('city_id')->nullable(); // Foreign key for cities
            $table->unsignedBigInteger('district_id')->nullable(); // Foreign key for districts
            // Foreign key constraints
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null'); // Set null if the country is deleted
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null'); // Set null if the city is deleted
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null'); // Set null if the district is deleted
            $table->foreign('education_level_id')->references('id')->on('education_levels')->onDelete('set null');
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('set null');
            $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('set null');
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
