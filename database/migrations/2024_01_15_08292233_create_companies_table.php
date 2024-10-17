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
            $table->foreignId('country_id')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->foreignId('company_size_id')->nullable();
            $table->foreignId('company_type_id')->nullable();
            $table->text('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('company_email')->nullable();
            $table->text('tax_code')->nullable();
            $table->date('date_of_establishment')->nullable();
            $table->text('working_days')->nullable();
            $table->text('overtime_policy')->nullable();
            $table->text('website')->nullable();
            $table->text('facebook')->nullable();
            $table->text('youtube')->nullable();
            $table->text('linked')->nullable();
            $table->text('logo')->nullable();
            $table->text('banner')->nullable();
            $table->text('address')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('description')->nullable();
            $table->boolean('approved')->default(false); // Trạng thái xác nhận, mặc định là chưa xác nhận
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
