<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_last_login', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->timestamp('last_login')->useCurrent()->useCurrentOnUpdate();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_last_login');
    }
};