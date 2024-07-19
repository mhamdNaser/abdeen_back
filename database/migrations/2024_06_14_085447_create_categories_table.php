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
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->string('en_name');
            $table->string('ar_name');
            $table->string('en_description');
            $table->string('ar_description');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('status');
            $table->boolean('in_menu');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
