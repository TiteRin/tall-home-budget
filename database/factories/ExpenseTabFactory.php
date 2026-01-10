<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseTab>
 */
class ExpenseTabFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'period_start_day' => $this->faker->numberBetween(1, 31),
            'period_end_day' => $this->faker->numberBetween(1, 31),
        ];
    }
}
