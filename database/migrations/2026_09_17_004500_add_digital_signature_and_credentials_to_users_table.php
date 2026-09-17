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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cmp', 20)->nullable()->after('specialty');
            $table->string('rne', 20)->nullable()->after('cmp');
            $table->string('firma_digital', 255)->nullable()->after('rne');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cmp', 'rne', 'firma_digital']);
        });
    }
};
