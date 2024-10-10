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
        Schema::create('statistic_applies', function (Blueprint $table) {
            $table->id();
            $table->integer('recruitments_id')->unsigned();
            $table->integer('application_count');
            $table->integer('browsing_count');
            $table->date('application_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_applies');
    }
};
