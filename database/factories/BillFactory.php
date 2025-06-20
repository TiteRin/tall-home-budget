<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\DistributionMethod;
use App\Models\Bill;

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
            'amount' => $this->faker->numberBetween(5000, 100000),
            'distribution_method' => $this->faker->randomElement(DistributionMethod::cases()),
            'household_id' => Household::factory()->create()->id,
            'household_member_id' => HouseholdMember::factory()->create()->id,
        ];
    }
}
