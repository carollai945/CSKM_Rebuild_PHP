<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startAt = now()->addDays($this->faker->numberBetween(1, 14))->setTime(9, 0);

        return [
            'staff_id' => Staff::factory(),
            'leave_type' => $this->faker->randomElement(['ANNUAL', 'SICK', 'PERSONAL', 'OTHER']),
            'start_at' => $startAt,
            'end_at' => (clone $startAt)->addHours(8),
            'reason' => $this->faker->sentence(),
            'status' => 'PENDING',
            'approved_by' => null,
            'reject_reason' => null,
        ];
    }
}
