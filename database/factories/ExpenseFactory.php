<?php

namespace Database\Factories;

use App\Enums\DistributionMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'amount' => $this->faker->numberBetween(1000, 20000),
            'spent_at' => now(),
            'distribution_method' => DistributionMethod::EQUAL
        ];
    }
}
