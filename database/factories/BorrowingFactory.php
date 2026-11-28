<?php

namespace Database\Factories;

use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $borrowDate = now()->startOfDay();

        return [
            'member_id' => Member::factory(),
            'admin_id' => User::factory()->petugas(),
            'book_id' => Book::factory(),
            'borrow_date' => $borrowDate,
            'due_date' => $borrowDate->copy()->addDays(7),
            'duration_days' => 7,
            'return_date' => null,
            'late_days' => 0,
            'status' => BorrowingStatus::Borrowed->value,
            'fine_amount' => 0,
        ];
    }

    /**
     * Transaksi yang sudah dikembalikan tepat waktu.
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'return_date' => now()->startOfDay(),
            'status' => BorrowingStatus::Returned->value,
        ]);
    }

    /**
     * Transaksi yang tercatat terlambat.
     */
    public function overdue(int $lateDays = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->startOfDay()->subDays($lateDays),
            'status' => BorrowingStatus::Overdue->value,
        ]);
    }

    /**
     * Transaksi buku hilang.
     */
    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'return_date' => now()->startOfDay(),
            'status' => BorrowingStatus::Lost->value,
        ]);
    }
}
