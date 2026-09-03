<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_cita', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_cita');
    }
};