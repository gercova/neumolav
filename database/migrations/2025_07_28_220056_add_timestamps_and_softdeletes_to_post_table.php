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
        Schema::table('post', function (Blueprint $table) {
            // Si ya existen estos campos, no los agregaremos de nuevo
            if (!Schema::hasColumn('post', 'created_at')) {
                $table->timestamps(); // Esto agrega created_at y updated_at
            }
            
            if (!Schema::hasColumn('post', 'deleted_at')) {
                $table->softDeletes(); // Esto agrega deleted_at
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post', function (Blueprint $table) {
            //
        });
    }
};
