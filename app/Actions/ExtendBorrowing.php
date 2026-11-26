<?php

namespace App\Actions;

use App\Models\Borrowing;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Memperpanjang masa peminjaman buku.
 *
 * Perpanjangan hanya berlaku bila buku belum terlambat dan jumlah
 * perpanjangan belum melebihi batas yang ditetapkan di pengaturan.
 */
class ExtendBorrowing
{
    /**
     * @throws ValidationException
     */
    public function handle(Borrowing $borrowing, ?int $adminId = null): Borrowing
    {
        return DB::transaction(function () use ($borrowing, $adminId): Borrowing {
            /** @var Borrowing $borrowing */
            $borrowing = Borrowing::query()
                ->whereKey($borrowing->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $borrowing->status->isActive() || $borrowing->return_date !== null) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya peminjaman aktif yang dapat diperpanjang.',
                ]);
            }

            if ($borrowing->isOverdue()) {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman yang sudah terlambat tidak dapat diperpanjang.',
                ]);
            }

            $maxExtensions = SystemSetting::getInt('maksimal_perpanjangan', 2);

            if ($borrowing->extension_count >= $maxExtensions) {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini sudah diperpanjang '.$maxExtensions.' kali dan tidak dapat diperpanjang lagi.',
                ]);
            }

            $extensionDays = SystemSetting::getInt('durasi_perpanjangan', 7);
            $dueDate = $borrowing->due_date?->copy()->startOfDay() ?? now()->startOfDay();

            $borrowing->update([
                'due_date' => $dueDate->addDays($extensionDays),
                'duration_days' => $borrowing->duration_days + $extensionDays,
                'extension_count' => $borrowing->extension_count + 1,
                'extended_at' => now(),
            ]);

            if ($adminId !== null) {
                $borrowing->forceFill(['admin_id' => $adminId])->save();
            }

            return $borrowing->refresh();
        });
    }
}
