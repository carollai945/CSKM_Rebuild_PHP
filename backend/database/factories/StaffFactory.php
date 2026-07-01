<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'staff_no'  => $this->faker->unique()->numerify('S###'),
            'name'      => $this->faker->name(),
            'status'    => 'ACTIVE',
            'join_date' => $this->faker->date(),
        ];
    }
}
