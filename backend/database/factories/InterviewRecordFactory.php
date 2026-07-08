<?php

namespace Database\Factories;

use App\Models\InterviewRecord;
use App\Models\Lead;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InterviewRecord>
 */
class InterviewRecordFactory extends Factory
{
    protected $model = InterviewRecord::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'staff_id' => Staff::factory(),
            'interview_date' => $this->faker->date(),
            'result_code' => $this->faker->randomElement(['INTERESTED', 'NOT_INTERESTED', 'CONVERTED']),
            'content' => $this->faker->sentence(),
            'next_contact_date' => $this->faker->optional()->date(),
        ];
    }
}
