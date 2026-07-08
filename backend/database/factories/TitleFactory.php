<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Title>
 */
class TitleFactory extends Factory
{
    protected $model = Title::class;

    public function definition(): array
    {
        return [
            'region_id' => null,
            'department_id' => Department::factory(),
            'title_no' => $this->faker->unique()->bothify('T###'),
            'title_name' => $this->faker->jobTitle(),
            'status' => 'active',
        ];
    }
}
