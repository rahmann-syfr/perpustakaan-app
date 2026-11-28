<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Borrowing;
use App\Models\FinePayment;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinePayment>
 */
class FinePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $borrowing = Borrowing::factory();

        return [
            'borrowing_id' => $borrowing,
            'member_id' => Member::factory(),
            'admin_id' => User::factory()->petugas(),
            'amount' => fake()->numberBetween(1000, 50000),
            'method' => PaymentMethod::Cash->value,
            'reference' => fake()->optional()->bothify('TRX-####-????'),
            'note' => null,
        ];
    }

    /**
     * Pembayaran penuh untuk seluruh denda transaksi.
     */
    public function fullPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => Borrowing::query()
                ->whereKey($attributes['borrowing_id'])
                ->value('fine_amount') ?? 0,
        ]);
    }
}
