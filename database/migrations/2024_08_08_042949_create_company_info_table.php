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
        Schema::create('company_info', function (Blueprint $table) {
            $table->id();
            $table->string('logo');
            $table->string('company_name');
            $table->text('company_description_ar')->nullable();
            $table->text('company_description_en')->nullable();
            $table->string('location')->default('Jordan / Aqaba');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('commercial_register')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_date')->nullable();
            $table->date('license_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_info');
    }
};
