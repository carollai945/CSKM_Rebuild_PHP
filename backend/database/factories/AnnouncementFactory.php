<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph(),
            'target_scope' => $this->faker->randomElement(['ALL', 'STAFF', 'STUDENTS']),
            'status' => 'DRAFT',
            'publish_at' => null,
        ];
    }
}
