<?php

namespace Database\Factories;

use App\Models\Household;
use App\Enums\DistributionMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class HouseholdFactory extends Factory
{
    protected $model = Household::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'has_joint_account' => $this->faker->boolean(),
            'default_distribution_method' => $this->faker->randomElement(DistributionMethod::cases()),
        ];
    }
} 