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
        Schema::table('historia_riesgo', function (Blueprint $table) {
            $table->unsignedBigInteger('id_historia')->nullable()->after('id');
            $table->foreign('id_historia')->references('id')->on('historias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historia_riesgo', function (Blueprint $table) {
            $table->dropForeign(['id_historia']);
            $table->dropColumn(['id_historia']);
        });
    }
};
