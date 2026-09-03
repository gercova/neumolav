<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {

        $users = [
            [
                'name'      => 'German Cotrina Valles', 
                'email'     => 'gcotrinav@neumotar.com', 
                'biografia' => 'Egresado de la Escuela Profesional de Ingeniería de Sistemas e Informática de la UNSM-T, especializado en programación, bases de datos.', 
                'specialty' => 1, 
                'username'  => 'gcotrinav', 
                'avatar'    => 'users/293991865_119906427421946_6742039180476277065_n.jpg',
                'password'  => Hash::make('123456789'),
            ],
            [
                'name'      => 'Wilter Aro Cotrina', 
                'email'     => 'waroc@neumotar.com', 
                'biografia' => 'Egresado de la Escuela Latinoamericana de Medicina (CUBA) el 2011 y formado como Médico Neumólogo en la Universidad Nacional Mayor de San Marcos, realizando prácticas médicas especializadas en el Hospital Nacional Edgardo Rebagliati Martins.', 
                'specialty' => 2, 
                'username'  => 'waroc', 
                'avatar'    => 'users/logo-neumotar-2025.png',
                'password'  => Hash::make('123456789'),
            ],
            [
                'name'      => 'Beky Bardales Perez', 
                'email'     => 'bbardalesp@neumotar.com', 
                'biografia' => null, 
                'specialty' => 3, 
                'username'  => 'bbardalesp', 
                'avatar'    => 'users/687.jpg',
                'password'  => Hash::make('123456789'),
            ],
            [
                'name'      => 'Geydith Daniela Chávez Espinoza', 
                'email'     => 'gdanielachveze@neumotar.com', 
                'biografia' => null, 
                'specialty' => 3, 
                'username'  => 'gdanielachveze', 
                'avatar'    => 'users/daniela.jpg',
                'password'  => Hash::make('123456789'),
            ]
        ];

        foreach($users as $item){
            User::create($item);
        }
    }
}
