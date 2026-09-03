<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion', 45)->nullable();
            $table->string('link', 100)->nullable();
            $table->unique(['descripcion', 'link']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};