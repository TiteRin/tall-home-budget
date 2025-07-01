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
        $faker = \Faker\Factory::create();

        return [
            'name' => $faker->company(),
            'has_joint_account' => $faker->boolean(),
            'default_distribution_method' => $faker->randomElement(DistributionMethod::cases()),
        ];
    }
}
