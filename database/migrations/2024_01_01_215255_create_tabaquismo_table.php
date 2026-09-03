<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabaquismo', function (Blueprint $table) {
            $table->increments('id');
            $table->text('consumo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabaquismo');
    }
};