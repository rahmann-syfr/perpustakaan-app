<?php

namespace App\Actions;

use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowBook
{
    /**
     * Proses peminjaman buku dalam satu transaksi database.
     *
     * Stok buku dikunci (lockForUpdate) agar dua petugas tidak bisa
     * meminjam buku yang sama pada waktu bersamaan.
     *
     * @param  array{member_id: int, book_id: int, duration_days: int}  $data
     */
    public function handle(array $data, ?int $adminId = null): Borrowing
    {
        return DB::transaction(function () use ($data, $adminId): Borrowing {
            $book = Book::query()
                ->whereKey($data['book_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($book->available_stock < 1) {
                throw ValidationException::withMessages([
                    'book_id' => 'Stok buku "'.$book->title.'" sedang kosong.',
                ]);
            }

            $durationDays = (int) $data['duration_days'];
            $borrowDate = now()->startOfDay();

            // Batas jumlah buku aktif per anggota diatur dari halaman pengaturan.
            $maxBooks = SystemSetting::getInt('maksimal_buku_anggota', 3);
            $activeCount = Borrowing::query()
                ->where('member_id', $data['member_id'])
                ->active()
                ->count();

            if ($activeCount >= $maxBooks) {
                throw ValidationException::withMessages([
                    'book_id' => 'Anggota sudah memiliki '.$activeCount.' buku aktif. Batas maksimal adalah '.$maxBooks.' buku.',
                ]);
            }

            $borrowing = Borrowing::create([
                'member_id' => $data['member_id'],
                'admin_id' => $adminId,
                'book_id' => $book->id,
                'borrow_date' => $borrowDate,
                'due_date' => $borrowDate->copy()->addDays($durationDays),
                'duration_days' => $durationDays,
                'status' => BorrowingStatus::Borrowed,
            ]);

            $book->decrement('available_stock');

            return $borrowing;
        });
    }
}
