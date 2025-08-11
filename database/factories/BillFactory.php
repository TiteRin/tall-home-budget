<?php

namespace Database\Factories;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
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
            'distribution_method' => DistributionMethod::EQUAL,
            'household_id' => Household::factory(),
            'member_id' => function (array $attributes) {
                // If the bill is being created with a null household_id
                // (e.g., in tests expecting validation to fail before DB),
                // do not create a member to avoid a premature DB exception.
                if (!array_key_exists('household_id', $attributes) || is_null($attributes['household_id'])) {
                    return null;
                }

                return Member::factory()->create([
                    'household_id' => $attributes['household_id'],
                ])->id;
            },
        ];
    }
}
