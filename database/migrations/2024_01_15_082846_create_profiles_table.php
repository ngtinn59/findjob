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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');

            $table->string("name", 100)->nullable();
            $table->string("phone", 20)->nullable();
            $table->string("email", 50)->nullable();
            $table->date("birthday")->nullable();
            $table->text("image")->nullable();
            $table->boolean("gender")->nullable();
            $table->string("address")->nullable();
            $table->unsignedBigInteger('country_id')->nullable(); // Foreign key for countries
            $table->unsignedBigInteger('city_id')->nullable(); // Foreign key for cities
            $table->unsignedBigInteger('district_id')->nullable(); // Foreign key for districts

            // Foreign key constraints
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null'); // Set null if the country is deleted
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null'); // Set null if the city is deleted
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null'); // Set null if the district is deleted

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
