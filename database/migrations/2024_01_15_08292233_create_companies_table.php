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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id');
            $table->foreignId('country_id');
            $table->foreignId('city_id');
            $table->foreignId('company_size_id')->nullable();
            $table->foreignId('company_type_id')->nullable();
            $table->text('name')->nullable();
            $table->string('phone')->nullable();
            $table->text('email')->nullable();
            $table->text('tax_code')->nullable();
            $table->date('Date_of_establishment')->nullable();
            $table->text('Working_days')->nullable();
            $table->text('Overtime_policy')->nullable();
            $table->text('webstie')->nullable();
            $table->text('facebook')->nullable();
            $table->text('logo')->nullable();
            $table->text('banner')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
