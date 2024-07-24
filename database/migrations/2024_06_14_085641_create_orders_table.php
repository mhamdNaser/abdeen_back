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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->comment('in_hold, in_delivery, finish, returned');
            $table->double('price');
            $table->double('total_price');
            $table->double('total_discount');
            $table->double('tax');
            $table->double('delivery');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
