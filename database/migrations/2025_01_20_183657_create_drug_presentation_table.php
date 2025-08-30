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
        Schema::create('droga_presentacion', function (Blueprint $table) {
            $table->unsignedBigInteger('id', 11);
            $table->string('descripcion', 100)->unique();
            $table->string('aka', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('droga_presentacion');
    }
};
