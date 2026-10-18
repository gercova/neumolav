<?php

namespace Database\Factories;

use App\Models\Occupation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Occupation>
 */
class OccupationFactory extends Factory
{
    protected $model = Occupation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'descripcion' => substr('Ocupacion '.fake()->unique()->jobTitle().' '.fake()->randomNumber(3), 0, 100),
            'estado' => true,
        ];
    }
}
