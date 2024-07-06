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
        Schema::create('product_previews', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->foreignId('user_id')->unsigned()->index();
            $table->foreignId('product_id')->unsigned()->index();
            $table->integer('rate');
            $table->string('nate')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_previews');
    }
};
