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
        Schema::create('attribute_tags', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->string('name');
            $table->string('description');
            $table->foreignId('attribute_id')->unsigned()->index();
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_tags');
    }
};
