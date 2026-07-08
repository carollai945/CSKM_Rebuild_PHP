<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'region_id' => Region::factory(),
            'department_no' => $this->faker->unique()->bothify('D###'),
            'department_name' => $this->faker->company().'部',
            'status' => 'active',
        ];
    }
}
