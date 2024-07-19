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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->string('sku')->unique()->nullable();
            $table->string('en_name');
            $table->string('ar_name');
            $table->string('en_description');
            $table->string('ar_description');
            $table->integer('cost_Price');
            $table->integer('public_price');
            $table->integer('discount');
            $table->integer('quantity');
            $table->integer('view_num')->default(0)->nullable();
            $table->integer('like_num')->default(0)->nullable();
            $table->integer('buy_num')->default(0)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->boolean('status');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
