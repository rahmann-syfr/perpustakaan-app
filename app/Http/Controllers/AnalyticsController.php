<?php

namespace App\Http\Controllers;

use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Menampilkan halaman laporan & analitik perpustakaan.
     */
    public function index(Request $request): View
    {
        $range = (int) $request->integer('range', 6);
        $range = in_array($range, [3, 6, 12], true) ? $range : 6;

        $startDate = now()->subMonths($range - 1)->startOfMonth();

        $borrowingsInRange = Borrowing::query()
            ->whereDate('borrow_date', '>=', $startDate->toDateString());

        return view('analytics.index', [
            'range' => $range,
            'monthlyChart' => $this->monthlyBorrowingChart($startDate, $range),
            'categoryChart' => $this->categoryDistribution(),
            'statusChart' => $this->statusDistribution(),
            'topBooks' => $this->topBorrowedBooks(),
            'topMembers' => $this->topBorrowingMembers(),
            'summary' => $this->summary($borrowingsInRange),
        ]);
    }

    /**
     * Data peminjaman per bulan (grafik batang).
     *
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    private function monthlyBorrowingChart(mixed $startDate, int $range): array
    {
        $rows = Borrowing::query()
            ->whereDate('borrow_date', '>=', $startDate->toDateString())
            ->get(['borrow_date'])
            ->groupBy(fn (Borrowing $borrowing) => $borrowing->borrow_date?->format('Y-m'))
            ->map(fn (Collection $group) => $group->count());

        $labels = [];
        $values = [];

        for ($i = 0; $i < $range; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M Y');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Distribusi buku per kategori (grafik donat).
     *
     * @return array{labels: array<int, string>, values: array<int, int>, colors: array<int, string>}
     */
    private function categoryDistribution(): array
    {
        $palette = ['#0d9488', '#6366f1', '#f59e0b', '#ec4899', '#22c55e', '#3b82f6', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316'];

        $rows = Book::query()
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $categories = Category::whereIn('id', $rows->keys())->pluck('name', 'id');

        $labels = [];
        $values = [];
        $colors = [];

        foreach ($rows as $categoryId => $total) {
            $labels[] = $categories[$categoryId] ?? 'Tanpa Kategori';
            $values[] = (int) $total;
            $colors[] = $palette[count($colors) % count($palette)];
        }

        return ['labels' => $labels, 'values' => $values, 'colors' => $colors];
    }

    /**
     * Distribusi status transaksi (grafik donat).
     *
     * @return array{labels: array<int, string>, values: array<int, int>, colors: array<int, string>}
     */
    private function statusDistribution(): array
    {
        $rows = Borrowing::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $colors = [
            BorrowingStatus::Borrowed->value => '#f59e0b',
            BorrowingStatus::Returned->value => '#22c55e',
            BorrowingStatus::Overdue->value => '#ef4444',
            BorrowingStatus::Lost->value => '#64748b',
        ];

        $labels = [];
        $values = [];
        $chartColors = [];

        foreach (BorrowingStatus::cases() as $status) {
            $labels[] = $status->label();
            $values[] = (int) ($rows[$status->value] ?? 0);
            $chartColors[] = $colors[$status->value];
        }

        return ['labels' => $labels, 'values' => $values, 'colors' => $chartColors];
    }

    /**
     * Buku paling banyak dipinjam.
     */
    private function topBorrowedBooks(): Collection
    {
        return Book::query()
            ->withCount('borrowings')
            ->has('borrowings')
            ->orderByDesc('borrowings_count')
            ->take(8)
            ->get();
    }

    /**
     * Anggota paling aktif dalam borrowing.
     */
    private function topBorrowingMembers(): Collection
    {
        return User::query()
            ->where('role', 'anggota')
            ->withCount('borrowings')
            ->has('borrowings')
            ->orderByDesc('borrowings_count')
            ->take(8)
            ->get();
    }

    /**
     * Ringkasan angka utama untuk kartu statistik.
     *
     * @return array<string, int|float>
     */
    private function summary(mixed $borrowingsInRange): array
    {
        $totalBorrowings = (clone $borrowingsInRange)->count();
        $returned = (clone $borrowingsInRange)->where('status', BorrowingStatus::Returned)->count();
        $lateReturns = (clone $borrowingsInRange)->where('late_days', '>', 0)->count();
        $totalFine = (clone $borrowingsInRange)->sum('fine_amount');

        $averageFine = $totalBorrowings > 0
            ? round((float) $totalFine / $totalBorrowings, 2)
            : 0.0;

        return [
            'total_borrowings' => $totalBorrowings,
            'returned' => $returned,
            'late_returns' => $lateReturns,
            'total_fine' => (int) $totalFine,
            'average_fine' => $averageFine,
            'active_borrowings' => Borrowing::query()->active()->count(),
            'total_books' => Book::query()->count(),
            'total_members' => User::query()->where('role', 'anggota')->count(),
        ];
    }
}
