<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\DistributionMethod;
use App\Models\Household;
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

        $household = Household::factory()->create();
        $member = Member::factory()->create([
            'household_id' => $household->id,
        ]);

        return [
            'name' => $this->faker->words(3, true),
            'amount' => $this->faker->numberBetween(1000, 20000),
            'distribution_method' => DistributionMethod::EQUAL,
            'household_id' => $household->id,
            'member_id' => $member->id,
        ];
    }
}
