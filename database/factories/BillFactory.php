<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\DistributionMethod;
use App\Models\Bill;
use App\Models\Member;

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
            'member_id' => Member::factory(),
        ];
    }
}
