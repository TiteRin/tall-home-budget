<?php

namespace Database\Factories;

use App\Enums\DistributionMethod;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
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
            'spent_on' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'amount' => $this->faker->numberBetween(500, 15000),
            'distribution_method' => $this->faker->randomElement(DistributionMethod::cases()),
        ];
    }
}
