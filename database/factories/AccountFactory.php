<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'available_balance_cents' => fake()->numberBetween(100_000, 1_000_000),
        ];
    }

    public function withAvailableBalance(int $availableBalanceCents): static
    {
        return $this->state(fn (array $attributes) => [
            'available_balance_cents' => $availableBalanceCents,
        ]);
    }
}
