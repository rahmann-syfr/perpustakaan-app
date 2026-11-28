<?php

namespace App\Queries;

use App\Models\Borrowing;
use Illuminate\Database\Eloquent\Builder;

/**
 * Query object untuk memfilter transaksi sirkulasi.
 *
 * Dipakai oleh halaman daftar sirkulasi maupun laporan PDF agar keduanya
 * selalu menampilkan data yang sama.
 */
class BorrowingFilter
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private readonly array $filters = []) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function make(array $filters = []): self
    {
        return new self($filters);
    }

    /**
     * @param  Builder<Borrowing>  $query
     * @return Builder<Borrowing>
     */
    public function apply(Builder $query): Builder
    {
        $now = now();
        $period = $this->filters['filter'] ?? null;

        if ($period === 'hari_ini') {
            $query->whereDate('borrow_date', $now->toDateString());
        } elseif ($period === 'bulan_ini') {
            $query->whereMonth('borrow_date', $now->month)
                ->whereYear('borrow_date', $now->year);
        } elseif ($period === 'tahun_ini') {
            $query->whereYear('borrow_date', $now->year);
        }

        $startDate = $this->filters['start_date'] ?? null;
        $endDate = $this->filters['end_date'] ?? null;

        if (! empty($startDate) && ! empty($endDate)) {
            $query->whereBetween('borrow_date', [$startDate, $endDate]);
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        $search = trim((string) ($this->filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->whereHas('member', fn (Builder $members) => $members->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('book', fn (Builder $books) => $books->where('title', 'like', "%{$search}%"));
            });
        }

        return $query;
    }
}
