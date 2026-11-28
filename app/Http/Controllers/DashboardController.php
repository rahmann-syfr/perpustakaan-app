<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\FinePayment;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard untuk staf perpustakaan (Admin & Petugas).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isStaff = $user->role->isStaff();
        $canViewMemberStats = $user->role->canManageUsers();

        // Menghitung jumlah JUDUL BUKU (bukan total stok fisik).
        $countBuku = Book::count();
        $countAnggota = $canViewMemberStats ? Member::count() : 0;

        // Rincian koleksi (maksimal 200 judul terbaru).
        $booksList = Book::query()
            ->orderBy('title')
            ->take(200)
            ->get();

        $dipinjamList = Borrowing::with(['member', 'book'])
            ->active()
            ->latest()
            ->take(200)
            ->get();

        $dendaList = Borrowing::with(['member', 'book'])
            ->where('fine_amount', '>', 0)
            ->latest()
            ->take(200)
            ->get();

        $anggotaList = Member::query()
            ->latest()
            ->take(200)
            ->get();

        $recentActivities = Borrowing::query()
            ->with(['member', 'book'])
            ->latest()
            ->take(6)
            ->get();

        $overdueBorrowings = Borrowing::query()
            ->with(['member', 'book'])
            ->overdue()
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $overdueCount = Borrowing::query()
            ->overdue()
            ->count();

        $paidFineTotal = (int) FinePayment::query()->sum('amount');

        return view('dashboard', [
            'isStaff' => $isStaff,
            'canViewMemberStats' => $canViewMemberStats,
            'countBuku' => $countBuku,
            'booksList' => $booksList,
            'countAnggota' => $countAnggota,
            'anggotaList' => $anggotaList,
            'countDipinjam' => $dipinjamList->count(),
            'dipinjamList' => $dipinjamList,
            'sumDenda' => $dendaList->sum('fine_amount'),
            'dendaList' => $dendaList,
            'pinjamanAktifSaya' => $dipinjamList->count(),
            'recentActivities' => $recentActivities,
            'overdueBorrowings' => $overdueBorrowings,
            'overdueCount' => $overdueCount,
            'paidFineTotal' => $paidFineTotal,
            'trendChart' => $this->trendChart(),
            'categoryChart' => $this->categoryChart(),
        ]);
    }

    /**
     * Tren peminjaman 14 hari terakhir untuk grafik dashboard.
     *
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    private function trendChart(): array
    {
        $startDate = now()->subDays(13)->startOfDay();

        $rows = Borrowing::query()
            ->whereDate('borrow_date', '>=', $startDate->toDateString())
            ->get(['borrow_date'])
            ->groupBy(fn (Borrowing $borrowing) => $borrowing->borrow_date?->format('Y-m-d'))
            ->map(fn ($group) => $group->count());

        $labels = [];
        $values = [];

        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);
            $labels[] = $date->format('d/m');
            $values[] = (int) ($rows[$date->format('Y-m-d')] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Distribusi kategori buku yang dipinjam untuk grafik dashboard.
     *
     * @return array{labels: array<int, string>, values: array<int, int>, colors: array<int, string>}
     */
    private function categoryChart(): array
    {
        $palette = ['#0d9488', '#6366f1', '#f59e0b', '#ec4899', '#22c55e', '#3b82f6', '#ef4444', '#8b5cf6'];

        $rows = Borrowing::query()
            ->with('book.category')
            ->active()
            ->get()
            ->groupBy(fn (Borrowing $borrowing) => $borrowing->book?->category?->name ?? 'Tanpa Kategori')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        $labels = [];
        $values = [];
        $colors = [];

        foreach ($rows as $categoryName => $total) {
            $labels[] = (string) $categoryName;
            $values[] = (int) $total;
            $colors[] = $palette[count($colors) % count($palette)];
        }

        return ['labels' => $labels, 'values' => $values, 'colors' => $colors];
    }
}
