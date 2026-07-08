<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'phone' => $this->faker->phoneNumber(),
            'mobile' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'education_level' => $this->faker->randomElement(['高中', '專科', '大學', '研究所']),
            'education_other' => null,
            'source_code' => $this->faker->randomElement(['WEB', 'REFERRAL', 'EVENT']),
            'region_id' => Region::factory(),
            'assigned_staff_id' => null,
            'status' => 'NEW',
            'created_by' => User::factory(),
        ];
    }
}
