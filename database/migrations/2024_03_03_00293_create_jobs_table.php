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
            $table->unsignedBigInteger('jobtype_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('title')->nullable();
            $table->text('profession')->nullable();
            $table->text('position')->nullable();
            $table->text('experience_years')->nullable();
            $table->text('work_address')->nullable();
            $table->text('employment_type')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('salary_from')->nullable();
            $table->integer('salary_to')->nullable();
            $table->text('education_level')->nullable();
            $table->date('last_date')->nullable();
            $table->text('description')->nullable();
            $table->text('skill_experience')->nullable();
            $table->text('benefits')->nullable();
            $table->text('city')->nullable();
            $table->text('district')->nullable();
            $table->text('work_location')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('contact_name')->nullable();
            $table->text('phone')->nullable();
			$table->text('email')->nullable();
            $table->integer('status')->nullable();
            $table->integer('featured')->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('jobtype_id')->references('id')->on('job_types')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

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
