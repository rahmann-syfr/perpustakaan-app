<?php

namespace App\Models;

use App\Enums\BorrowingStatus;
use Database\Factories\BorrowingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    /** @use HasFactory<BorrowingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'member_id',
        'admin_id',
        'book_id',
        'borrow_date',
        'due_date',
        'duration_days',
        'extension_count',
        'extended_at',
        'return_date',
        'late_days',
        'status',
        'fine_amount',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'duration_days' => 'integer',
            'extension_count' => 'integer',
            'extended_at' => 'datetime',
            'late_days' => 'integer',
            'fine_amount' => 'integer',
            'status' => BorrowingStatus::class,
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function finePayments(): HasMany
    {
        return $this->hasMany(FinePayment::class);
    }

    /**
     * Peminjaman yang masih aktif (buku belum dikembalikan).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [BorrowingStatus::Borrowed->value, BorrowingStatus::Overdue->value])
            ->whereNull('return_date');
    }

    /**
     * Peminjaman yang sudah lewat tenggat namun belum dikembalikan.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->active()
            ->whereDate('due_date', '<', now()->toDateString());
    }

    /**
     * True jika buku masih dipinjam dan sudah melewati tanggal tenggat.
     *
     * Status "overdue" juga dianggap terlambat karena dipakai untuk
     * menandai transaksi yang sudah lewat tenggat.
     */
    public function isOverdue(): bool
    {
        if ($this->return_date !== null || $this->status->isFinished()) {
            return false;
        }

        if ($this->status === BorrowingStatus::Overdue) {
            return true;
        }

        return $this->due_date !== null
            && $this->due_date->copy()->endOfDay()->isPast();
    }

    /**
     * True jika transaksi masih berjalan (buku belum kembali).
     */
    public function isActive(): bool
    {
        return $this->return_date === null && $this->status->isActive();
    }

    /**
     * Total denda yang sudah dibayar untuk transaksi ini.
     */
    public function paidFine(): int
    {
        return (int) $this->finePayments()->sum('amount');
    }

    /**
     * Sisa denda yang belum dibayar.
     */
    public function outstandingFine(): int
    {
        return max(0, (int) $this->fine_amount - $this->paidFine());
    }

    /**
     * True jika sisa denda masih belum lunas.
     */
    public function hasOutstandingFine(): bool
    {
        return $this->outstandingFine() > 0;
    }

    /**
     * Sisa hari sebelum tanggal tenggat (negatif bila sudah terlambat).
     */
    public function daysUntilDue(): int
    {
        if ($this->due_date === null) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->due_date->copy()->startOfDay(), false);
    }
}
