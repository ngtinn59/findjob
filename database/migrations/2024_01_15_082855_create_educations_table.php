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
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->string("degree",100)->nullable();
            $table->string("institution",100)->nullable();
            $table->date("start_date",30)->nullable();
            $table->date("end_date",30)->nullable();
            $table->string('additionalDetail',100)->nullable();
            $table->unsignedBigInteger('profiles_id');
            $table->foreign('profiles_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
