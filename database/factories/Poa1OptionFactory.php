<?php

namespace Database\Factories;

use App\Models\Poa1Option;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poa1Option>
 */
class Poa1OptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('POA1_????')),
            'name' => fake()->words(2, true),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
