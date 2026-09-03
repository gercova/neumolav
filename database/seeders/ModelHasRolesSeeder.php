<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'role_id' => 1,
                'model_type' => 'App\\Models\\User',
                'model_id' => 1
            ],
            [
                'role_id' => 2,
                'model_type' => 'App\\Models\\User',
                'model_id' => 3
            ],
            [
                'role_id' => 2,
                'model_type' => 'App\\Models\\User',
                'model_id' => 4
            ],
            [
                'role_id' => 2,
                'model_type' => 'App\\Models\\User',
                'model_id' => 6
            ],
            [
                'role_id' => 3,
                'model_type' => 'App\\Models\\User',
                'model_id' => 7
            ],
            [
                'role_id' => 3,
                'model_type' => 'App\\Models\\User',
                'model_id' => 8
            ],
        ];

        foreach ($data as $item) {
            DB::table('model_has_roles')->insert($item);
        }
    }
}
