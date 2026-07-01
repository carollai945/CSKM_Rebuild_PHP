<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'region_code' => strtoupper($this->faker->unique()->lexify('R???')),
            'region_name' => $this->faker->city().'區',
            'region_english_name' => $this->faker->city().' Region',
            'abbr' => strtoupper($this->faker->lexify('??')),
            'status' => 'active',
        ];
    }
}
