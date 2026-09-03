<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnterpriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enterprise::create([
            'razon_social'      => 'Medytarq SAC',
            'nombre_comercial'  => 'Neumotar',
            'ruc'               => '20605677593',
            'email'             => 'atencion-citas@neumotar.com',
            'descripcion'       => 'Somos una empresa especializada en el diagnóstico, seguimiento y tratamiento de enfermedades respiratorias, con más de 4 años en el campo de la salud',
            'frase'             => 'Somos una empresa especializada en el diagnóstico, seguimiento y tratamiento de enfermedades respiratorias, con más de 4 años en el campo de la salud',
            'mision'            => "Ser para el 2030 el centro médico de referencia nacional y el guardián indiscutible de la salud respiratoria en la Amazonía peruana, reconocidos por nuestra innovación en servicios, nuestra profunda comprensión de las enfermedades de la región y nuestro compromiso inquebrantable con las comunidades que servimos.",
            'vision'            => "Ofrecer el mejor servicio de salud, con atención personalizada, humana y profesional; utilizando la mejor tecnología e innovación para mejorar la calidad de vida de nuestros pacientes",
            'ubigeo'            => '220101',
            'iframe_location'   => '<iframe src="https://maps.google.com/maps?width=100%25&amp;height=200&amp;hl=es&amp;q=Jiron%20Paraguay%20136,%20Tarapoto%2022202,%20Per%C3%BA+(NEUMOTAR)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>',
            'direccion'         => 'Jr. Paraguay 136',
            'pais'              => 'Perú',
            'codigo_pais'       => '+51',
            'telefono'          => '901222530',
            'pagina_web'        => 'neumotar.com',
            'representante_legal' => 'Wilter Aro Cotrina',
            'foto_representante' => 'photos/SYKBRNgFiH.png',
            'logo'              => 'photos/aCo0WfWRKp.png',
            'logo_mini'         => 'photos/Qqazu1FtrB.png',
            'logo_receta'       => 'photos/ij6HToWyXY.png',
            'rubro'             => 'SERVICIOS CLÍNICOS Y HOSPITALARIOS',
            'fecha_creacion'    => '2022-12-08',
            'autocomplete'      => 'on'
        ]);
    }
}
