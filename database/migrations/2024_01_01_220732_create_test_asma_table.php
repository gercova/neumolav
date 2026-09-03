<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_asma', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 200);
            $table->string('wpp', 12)->nullable();
            $table->integer('result')->length(2);
            $table->string('diagnosis', 50);
            $table->timestamp('fecha')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_asma');
    }
};