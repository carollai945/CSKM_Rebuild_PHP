<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course_name' => $this->faker->words(3, true).'課程',
            'course_code' => strtoupper($this->faker->unique()->bothify('C##')),
            'description' => $this->faker->sentence(),
            'status' => 'ACTIVE',
        ];
    }
}
