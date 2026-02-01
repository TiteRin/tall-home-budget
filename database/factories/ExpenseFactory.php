<?php

namespace Database\Factories;

use App\Enums\DistributionMethod;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Member;
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
            'expense_tab_id' => ExpenseTab::factory(),
            'member_id' => function (array $attributes) {
                if (!array_key_exists('expense_tab_id', $attributes) || is_null($attributes['expense_tab_id'])) {
                    return null;
                }

                $expenseTab = ExpenseTab::find($attributes['expense_tab_id']);

                return Member::factory()->create([
                    'household_id' => $expenseTab->household_id,
                ])->id;
            },
        ];
    }
}
