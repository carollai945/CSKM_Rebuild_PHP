<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'fee_item_id' => null,
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'currency' => 'TWD',
            'payment_method' => $this->faker->randomElement(['CASH', 'TRANSFER', 'CARD']),
            'payment_date' => $this->faker->date(),
            'status' => 'PENDING',
            'finance_confirmed_by' => null,
            'academic_confirmed_by' => null,
            'note' => $this->faker->sentence(),
        ];
    }
}
