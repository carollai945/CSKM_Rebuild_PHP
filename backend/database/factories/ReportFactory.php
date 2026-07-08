<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'report_type' => $this->faker->randomElement(['DAILY', 'WEEKLY']),
            'report_date' => $this->faker->date(),
            'content' => $this->faker->paragraph(),
            'status' => 'DRAFT',
        ];
    }
}
