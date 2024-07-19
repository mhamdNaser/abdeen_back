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
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->foreignId('order_id')->unsigned()->index();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreignId('tag_id')->nullable();
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('attribute_tags')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
