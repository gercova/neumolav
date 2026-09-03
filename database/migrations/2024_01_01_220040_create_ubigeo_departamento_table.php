<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubigeo_departamento', function (Blueprint $table) {
            $table->char('id', 2)->primary();
            $table->string('name', 45);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubigeo_departamento');
    }
};