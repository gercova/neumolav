<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla de catálogo tipos_atencion
        Schema::create('tipos_atencion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion', 50);
            $table->string('codigo', 20)->unique();
            $table->string('badge_color', 20)->default('secondary');
            $table->timestamps();
        });

        // Insertar datos base antes de crear las FKs
        DB::table('tipos_atencion')->insert([
            [
                'id'          => 1,
                'descripcion' => 'Nuevo',
                'codigo'      => 'NUEVO',
                'badge_color' => 'success',
                'created_at'  => now(),
                'updated_at'  => now()
            ],
            [
                'id'          => 2,
                'descripcion' => 'Control',
                'codigo'      => 'CONTROL',
                'badge_color' => 'primary',
                'created_at'  => now(),
                'updated_at'  => now()
            ],
            [
                'id'          => 3,
                'descripcion' => 'Continuador',
                'codigo'      => 'CONTINUADOR',
                'badge_color' => 'warning',
                'created_at'  => now(),
                'updated_at'  => now()
            ],
        ]);

        // 2. Agregar columna id_tipo_atencion en citas
        Schema::table('citas', function (Blueprint $table) {
            $table->unsignedInteger('id_tipo_atencion')->default(1)->after('id_historia');
            $table->foreign('id_tipo_atencion')->references('id')->on('tipos_atencion')->onDelete('restrict');
            $table->index('id_tipo_atencion');
        });

        // 3. Agregar columna id_tipo_atencion en historias
        Schema::table('historias', function (Blueprint $table) {
            $table->unsignedInteger('id_tipo_atencion')->default(1)->after('id_ct');
            $table->foreign('id_tipo_atencion')->references('id')->on('tipos_atencion')->onDelete('restrict');
            $table->index('id_tipo_atencion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historias', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_atencion']);
            $table->dropIndex(['id_tipo_atencion']);
            $table->dropColumn('id_tipo_atencion');
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_atencion']);
            $table->dropIndex(['id_tipo_atencion']);
            $table->dropColumn('id_tipo_atencion');
        });

        Schema::dropIfExists('tipos_atencion');
    }
};
