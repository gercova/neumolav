<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sexo', function (Blueprint $table) {
            $table->char('id', 1)->primary();
            $table->string('descripcion', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sexo');
    }
};