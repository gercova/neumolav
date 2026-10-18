<?php

namespace Database\Factories;

use App\Models\Diagnostic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diagnostic>
 */
class DiagnosticFactory extends Factory
{
    protected $model = Diagnostic::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'descripcion' => substr('Dx '.fake()->unique()->word().' '.fake()->randomNumber(4), 0, 50),
        ];
    }
}
