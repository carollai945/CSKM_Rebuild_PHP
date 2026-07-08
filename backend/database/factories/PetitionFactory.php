<?php

namespace Database\Factories;

use App\Models\Petition;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Petition>
 */
class PetitionFactory extends Factory
{
    protected $model = Petition::class;

    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph(),
            'status' => 'PENDING',
            'approved_by' => null,
            'reject_reason' => null,
        ];
    }
}
