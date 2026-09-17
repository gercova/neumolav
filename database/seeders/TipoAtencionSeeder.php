<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtencionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'id'          => 1,
                'descripcion' => 'Nuevo',
                'codigo'      => 'NUEVO',
                'badge_color' => 'success',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 2,
                'descripcion' => 'Control',
                'codigo'      => 'CONTROL',
                'badge_color' => 'primary',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 3,
                'descripcion' => 'Continuador',
                'codigo'      => 'CONTINUADOR',
                'badge_color' => 'warning',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_atencion')->updateOrInsert(
                ['id' => $tipo['id']],
                $tipo
            );
        }
    }
}
