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
            $table->string("name",100)->nullable();
            $table->string("title",100)->nullable();
            $table->string("phone",20)->nullable();
            $table->string("email",50)->nullable();
            $table->date("birthday")->nullable();
            $table->text("image")->nullable();
            $table->boolean("gender")->nullable();
            $table->string("location")->nullable();
            $table->string("website",100)->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
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
