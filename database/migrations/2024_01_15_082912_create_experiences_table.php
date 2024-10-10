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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string("position")->nullable();
            $table->string("company")->nullable();
            $table->string("start_date",100)->nullable();
            $table->string("end_date",100)->nullable();
            $table->string("responsibilities")->nullable();
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
        Schema::dropIfExists('experiences');
    }
};
