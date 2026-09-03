<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbigeoDepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datos = [
            ['id' => '01', 'name' => 'AMAZONAS'],
            ['id' => '02', 'name' => 'ÁNCASH'],
            ['id' => '03', 'name' => 'APURÍMAC'],
            ['id' => '04', 'name' => 'AREQUIPA'],
            ['id' => '05', 'name' => 'AYACUCHO'],
            ['id' => '06', 'name' => 'CAJAMARCA'],
            ['id' => '07', 'name' => 'CALLAO'],
            ['id' => '08', 'name' => 'CUSCO'],
            ['id' => '09', 'name' => 'HUANCAVELICA'],
            ['id' => '10', 'name' => 'HUÁNUCO'],
            ['id' => '11', 'name' => 'ICA'],
            ['id' => '12', 'name' => 'JUNÍN'],
            ['id' => '13', 'name' => 'LA LIBERTAD'],
            ['id' => '14', 'name' => 'LAMBAYEQUE'],
            ['id' => '15', 'name' => 'LIMA'],
            ['id' => '16', 'name' => 'LORETO'],
            ['id' => '17', 'name' => 'MADRE DE DIOS'],
            ['id' => '18', 'name' => 'MOQUEGUA'],
            ['id' => '19', 'name' => 'PASCO'],
            ['id' => '20', 'name' => 'PIURA'],
            ['id' => '21', 'name' => 'PUNO'],
            ['id' => '22', 'name' => 'SAN MARTÍN'],
            ['id' => '23', 'name' => 'TACNA'],
            ['id' => '24', 'name' => 'TUMBES'],
            ['id' => '25', 'name' => 'UCAYALI'],
        ];

        // Insertar datos usando Query Builder
        DB::table('ubigeo_departamento')->insert($datos);
    }
}
