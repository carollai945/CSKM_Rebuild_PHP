<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'region_id' => Region::factory(),
            'classroom_name' => $this->faker->numberBetween(101, 999).'教室',
            'capacity' => $this->faker->numberBetween(10, 60),
            'status' => 'ACTIVE',
        ];
    }
}
