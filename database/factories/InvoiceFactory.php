<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'reference' => fake()->unique()->bothify('INV-####??'),
            'amount_cents' => fake()->numberBetween(1_000, 50_000),
            'status' => 'pending',
            'settled_at' => null,
        ];
    }

    public function settled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'settled',
            'settled_at' => now(),
        ]);
    }
}
