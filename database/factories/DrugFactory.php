<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Drug;
use App\Models\Presentation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Drug>
 */
class DrugFactory extends Factory
{
    protected $model = Drug::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_categoria' => Category::factory(),
            'id_presentacion' => Presentation::factory(),
            'descripcion' => fake()->unique()->word().' '.fake()->randomNumber(4),
            'detalle' => fake()->sentence(),
            'estado' => true,
        ];
    }
}
