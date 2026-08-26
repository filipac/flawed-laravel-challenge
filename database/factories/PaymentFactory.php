<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'amount_cents' => fake()->numberBetween(1_000, 50_000),
            'fee_cents' => fake()->numberBetween(0, 2_500),
            'processed_by' => fake()->name(),
        ];
    }
}
