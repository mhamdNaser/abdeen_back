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
        Schema::create('permission_roles', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->unique();
            $table->foreignId('role_id')->unsigned()->index();
            $table->foreignId('permission_id')->unsigned()->index();
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('admin_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_roles');
    }
};
