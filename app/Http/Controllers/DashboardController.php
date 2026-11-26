<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\FinePayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard. Data yang ditampilkan dibatasi sesuai role pengguna
     * agar anggota tidak memuat seluruh data anggota, peminjaman, dan denda.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isStaff = $user->role->isStaff();
        $canViewMemberStats = $user->role->canManageUsers();

        // Menghitung jumlah JUDUL BUKU (bukan total stok fisik).
        $countBuku = Book::count();
        $countAnggota = $canViewMemberStats
            ? User::query()->where('role', Role::Anggota->value)->count()
            : 0;

        // Rincian koleksi (maksimal 200 judul terbaru).
        $booksList = Book::query()
            ->orderBy('title')
            ->take(200)
            ->get();

        if ($isStaff) {
            $dipinjamList = Borrowing::with(['user', 'book'])
                ->active()
                ->latest()
                ->take(200)
                ->get();

            $dendaList = Borrowing::with(['user', 'book'])
                ->where('fine_amount', '>', 0)
                ->latest()
                ->take(200)
                ->get();

            $anggotaList = User::query()
                ->where('role', Role::Anggota->value)
                ->latest()
                ->take(200)
                ->get();
        } else {
            // Anggota hanya melihat transaksi miliknya sendiri.
            $dipinjamList = Borrowing::with(['user', 'book'])
                ->where('user_id', $user->id)
                ->active()
                ->latest()
                ->get();

            $dendaList = Borrowing::with(['user', 'book'])
                ->where('user_id', $user->id)
                ->where('fine_amount', '>', 0)
                ->latest()
                ->get();

            // Anggota tidak boleh melihat daftar anggota lain.
            $anggotaList = collect();
        }

        $recentActivities = Borrowing::query()
            ->with(['user', 'book'])
            ->when(! $isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->take(6)
            ->get();

        $overdueBorrowings = Borrowing::query()
            ->with(['user', 'book'])
            ->when(! $isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->overdue()
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $overdueCount = Borrowing::query()
            ->when(! $isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->overdue()
            ->count();

        $paidFineTotal = (int) FinePayment::query()
            ->when(! $isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->sum('amount');

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
            'trendChart' => $this->trendChart($user->id, $isStaff),
            'categoryChart' => $this->categoryChart($user->id, $isStaff),
        ]);
    }

    /**
     * Tren peminjaman 14 hari terakhir untuk grafik dashboard.
     *
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    private function trendChart(int $userId, bool $isStaff): array
    {
        $startDate = now()->subDays(13)->startOfDay();

        $rows = Borrowing::query()
            ->when(! $isStaff, fn ($query) => $query->where('user_id', $userId))
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
    private function categoryChart(int $userId, bool $isStaff): array
    {
        $palette = ['#0d9488', '#6366f1', '#f59e0b', '#ec4899', '#22c55e', '#3b82f6', '#ef4444', '#8b5cf6'];

        $rows = Borrowing::query()
            ->with('book.category')
            ->when(! $isStaff, fn ($query) => $query->where('user_id', $userId))
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
