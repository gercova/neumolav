<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('droga_presentacion', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 100)->unique();
            $table->string('aka', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('droga_presentacion');
    }
};