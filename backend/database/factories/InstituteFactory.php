<?php

namespace Database\Factories;

use App\Models\Institute;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Institute>
 */
class InstituteFactory extends Factory
{
    protected $model = Institute::class;

    public function definition(): array
    {
        return [
            'region_id' => Region::factory(),
            'institute_name' => $this->faker->city().'校區',
            'institute_code' => strtoupper($this->faker->unique()->bothify('I##')),
            'status' => 'ACTIVE',
        ];
    }
}
