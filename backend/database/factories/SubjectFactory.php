<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'subject_name' => $this->faker->words(2, true),
            'subject_code' => strtoupper($this->faker->unique()->bothify('S##')),
            'status' => 'ACTIVE',
        ];
    }
}
