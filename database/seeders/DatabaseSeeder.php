<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Enterprise;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(EstadoCitaSeeder::class);
        $this->call(EstadoCivilSeeder::class);
        $this->call(OcupacionSeeder::class);
        $this->call(EspecialidadSeeder::class);
        $this->call(UbigeoDepartamentoSeeder::class);
        $this->call(UbigeoProvinciaSeeder::class);
        $this->call(UbigeoDistritoSeeder::class);
        $this->call(DrogaCategoriaSeeder::class);
        $this->call(DrogaPresentacionSeeder::class);
        $this->call(DrogasSeeder::class);
        $this->call(DiagnosticoSeeder::class);
        $this->call(TipoExamenSeeder::class);
        $this->call(TipoPostSeeder::class);
        $this->call(GSGISeeder::class);
        $this->call(TipoDocumentoSeeder::class);
        $this->call(TabaquismoSeeder::class);
        // $this->call(ModuleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(EnterpriseSeeder::class);
    }
}
