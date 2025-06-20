<?php

namespace Database\Factories;

use App\Models\Household;
use App\Models\HouseholdMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class HouseholdMemberFactory extends Factory
{
    protected $model = HouseholdMember::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'household_id' => Household::factory(),
        ];
    }
} 