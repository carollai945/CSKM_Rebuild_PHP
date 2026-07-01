<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_no' => $this->faker->unique()->numerify('ST####'),
            'name'       => $this->faker->name(),
            'status'     => 'ACTIVE',
        ];
    }
}
