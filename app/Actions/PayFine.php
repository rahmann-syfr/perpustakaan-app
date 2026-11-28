<?php

namespace App\Actions;

use App\Enums\PaymentMethod;
use App\Models\Borrowing;
use App\Models\FinePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Mencatat pembayaran denda keterlambatan.
 *
 * Pembayaran dicatat sebagai record terpisah (tidak mengurangi
 * `fine_amount` transaksi) sehingga riwayat pembayaran tetap dapat
 * diaudit untuk setiap transaksi.
 */
class PayFine
{
    /**
     * @throws ValidationException
     */
    public function handle(
        Borrowing $borrowing,
        int $amount,
        PaymentMethod $method,
        ?int $adminId = null,
        ?string $reference = null,
        ?string $note = null,
    ): FinePayment {
        return DB::transaction(function () use ($borrowing, $amount, $method, $adminId, $reference, $note): FinePayment {
            /** @var Borrowing $borrowing */
            $borrowing = Borrowing::query()
                ->whereKey($borrowing->id)
                ->lockForUpdate()
                ->firstOrFail();

            $outstanding = $borrowing->outstandingFine();

            if ($outstanding <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Transaksi ini tidak memiliki denda yang outstanding.',
                ]);
            }

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal pembayaran harus lebih besar dari nol.',
                ]);
            }

            if ($amount > $outstanding) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal pembayaran melebihi sisa denda (Rp'.number_format($outstanding, 0, ',', '.').').',
                ]);
            }

            return FinePayment::create([
                'borrowing_id' => $borrowing->id,
                'member_id' => $borrowing->member_id,
                'admin_id' => $adminId,
                'amount' => $amount,
                'method' => $method->value,
                'reference' => $reference,
                'note' => $note,
            ]);
        });
    }
}
