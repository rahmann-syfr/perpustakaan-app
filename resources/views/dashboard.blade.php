@extends('layouts.app')

@section('title', 'Dashboard - PerpusApp')
@section('page-title', 'Dashboard Perpustakaan')

@section('content')
@php
    $currentUser = Auth::user();
    $isStaff = $currentUser->role->isStaff();
    $canViewMemberStats = $canViewMemberStats ?? $currentUser->role->canManageUsers();

    // Seluruh data statistik & rincian modal sudah disiapkan oleh
    // DashboardController agar query tidak terduplikasi di layer view.
    $booksList = $booksList ?? collect();
    $anggotaList = $anggotaList ?? collect();
    $dipinjamList = $dipinjamList ?? collect();
    $dendaList = $dendaList ?? collect();

    $countBuku = $countBuku ?? $booksList->count();
    $countAnggota = $countAnggota ?? $anggotaList->count();
    $countDipinjam = $countDipinjam ?? $dipinjamList->count();
    $sumDenda = $sumDenda ?? $dendaList->sum('fine_amount');

    $pinjamanAktifSaya = $pinjamanAktifSaya ?? $dipinjamList->count();
    $recentActivities = $recentActivities ?? collect();
    $overdueBorrowings = $overdueBorrowings ?? collect();
    $overdueCount = $overdueCount ?? 0;
@endphp

<section class="row">
    <div class="col-12">
        
        <!-- Banner Selamat Datang -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card perpus-welcome-card mb-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl bg-primary me-3 me-md-4 flex-shrink-0">
                                    <span class="avatar-content text-uppercase fs-3 fw-bold">{{ substr($currentUser->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Selamat datang kembali, {{ $currentUser->name }}!</h4>
                                    <p class="mb-0 text-muted">
                                        Anda login sebagai 
                                        <span class="badge bg-light-primary text-primary fw-semibold">{{ $currentUser->role->label() }}</span>
                                        @if(!$isStaff)
                                            &bull; Sedang meminjam <strong class="text-dark">{{ $pinjamanAktifSaya }} Buku</strong>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Aksi Cepat Sesuai Role -->
                            <div class="d-flex flex-wrap gap-2">
                                @if($isStaff)
                                    <a href="{{ route('borrowings.index') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-left-right me-1"></i> Kelola Sirkulasi
                                    </a>
                                    <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-book-half me-1"></i> Data Buku
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Kartu Statistik Interaktif (Bisa Diklik Semua Role) -->
        <div class="row g-3">
            <!-- 1. Total Koleksi -->
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card card-stat card-clickable h-100 mb-0"
                     data-bs-toggle="modal"
                     data-bs-target="#koleksiBukuModal"
                     title="Klik untuk melihat rincian koleksi buku">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-book-half"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold mb-1">Total Koleksi</h6>
                                <h5 class="font-extrabold mb-1">{{ $countBuku }} Judul</h5>
                                <span class="text-primary fw-semibold" style="font-size: 0.72rem;">
                                    <i class="bi bi-eye me-1"></i>Lihat rincian
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($canViewMemberStats)
            <!-- 2. Total Anggota -->
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card card-stat card-clickable h-100 mb-0"
                     data-bs-toggle="modal"
                     data-bs-target="#anggotaModal"
                     title="Klik untuk melihat daftar anggota">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold mb-1">Total Anggota</h6>
                                <h5 class="font-extrabold mb-1">{{ $countAnggota }}</h5>
                                <span class="text-info fw-semibold" style="font-size: 0.72rem;">
                                    <i class="bi bi-eye me-1"></i>Khusus peminjam
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- 3. Buku Dipinjam -->
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card card-stat card-clickable h-100 mb-0"
                     data-bs-toggle="modal"
                     data-bs-target="#bukuDipinjamModal"
                     title="Klik untuk melihat daftar buku yang sedang dipinjam">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold mb-1">Buku Dipinjam</h6>
                                <h5 class="font-extrabold mb-1">{{ $countDipinjam }}</h5>
                                <span class="text-success fw-semibold" style="font-size: 0.72rem;">
                                    <i class="bi bi-eye me-1"></i>Lihat rincian
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Total Denda -->
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card card-stat card-clickable h-100 mb-0"
                     data-bs-toggle="modal"
                     data-bs-target="#totalDendaModal"
                     title="Klik untuk melihat rincian transaksi denda">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon red mb-2">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold mb-1">Total Denda</h6>
                                <h5 class="font-extrabold mb-1">Rp{{ number_format($sumDenda, 0, ',', '.') }}</h5>
                                <span class="text-danger fw-semibold" style="font-size: 0.72rem;">
                                    <i class="bi bi-eye me-1"></i>Lihat rincian
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 5. Denda Terbayar -->
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card card-stat card-clickable h-100 mb-0"
                     data-bs-toggle="modal"
                     data-bs-target="#totalDendaModal"
                     title="Klik untuk melihat rincian transaksi denda">
                    <div class="card-body px-4 py-4-5">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold mb-1">Denda Terbayar</h6>
                                <h5 class="font-extrabold mb-1">Rp{{ number_format($paidFineTotal ?? 0, 0, ',', '.') }}</h5>
                                <span class="text-success fw-semibold" style="font-size: 0.72rem;">
                                    <i class="bi bi-eye me-1"></i>Lihat rincian
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($overdueCount > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger border-0 perpus-alert-overdue mb-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <strong><i class="bi bi-exclamation-octagon-fill me-1"></i>
                            {{ $overdueCount }} peminjaman melewati tenggat</strong>
                    </div>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-danger fw-semibold">
                        Buka Sirkulasi
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-graph-up text-primary me-1"></i> Tren Peminjaman 14 Hari</h5>
                        <span class="badge bg-light-primary text-primary">Ringkasan</span>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 260px;">
                            <canvas id="dashboardTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100 mb-0">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-pie-chart text-primary me-1"></i> Kategori Dipinjam</h5>
                    </div>
                    <div class="card-body">
                        @if(count($categoryChart['values']) > 0)
                            <div style="position: relative; height: 260px;">
                                <canvas id="dashboardCategoryChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-pie-chart d-block fs-2 mb-2 opacity-50"></i>
                                Belum ada buku yang dipinjam.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-2">
            <div class="col-lg-7">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Aktivitas Terbaru</h5>
                        <span class="badge bg-light-primary text-primary">{{ $recentActivities->count() }} transaksi</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush perpus-activity-list">
                            @forelse($recentActivities as $activity)
                                <li class="list-group-item px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $activity->book->title ?? 'Buku dihapus' }}
                                            </div>
                                            <small class="text-muted">
                                                @if($isStaff)
                                                    {{ $activity->member->name ?? 'Anggota' }} &bull;
                                                @endif
                                                {{ $activity->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <span class="badge {{ $activity->status->badgeClass() }} fw-semibold">
                                            {{ $activity->status->label() }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-4">Belum ada aktivitas peminjaman.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100 mb-0">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-alarm text-danger me-1"></i> Prioritas Kembali</h5>
                    </div>
                    <div class="card-body">
                        @forelse($overdueBorrowings as $item)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $item->book->title ?? '-' }}</div>
                                    @if($isStaff)
                                        <small class="text-muted">{{ $item->member->name ?? '-' }}</small>
                                    @endif
                                    <small class="text-danger d-block">Tenggat {{ $item->due_date?->format('d M Y') }}</small>
                                </div>
                                <i class="bi bi-exclamation-triangle text-danger"></i>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0"><i class="bi bi-emoji-smile text-success me-1"></i> Semua buku tepat waktu!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/extensions/chart.js/chart.umd.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
        Chart.defaults.color = '#64748b';

        const trendCanvas = document.getElementById('dashboardTrendChart');
        if (trendCanvas) {
            const gradient = trendCanvas.getContext('2d').createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, 'rgba(13, 148, 136, 0.35)');
            gradient.addColorStop(1, 'rgba(13, 148, 136, 0)');

            new Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: @json($trendChart['labels']),
                    datasets: [{
                        label: 'Peminjaman',
                        data: @json($trendChart['values']),
                        borderColor: '#0d9488',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        borderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, border: { display: false } },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.18)' },
                            border: { display: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        const categoryCanvas = document.getElementById('dashboardCategoryChart');
        if (categoryCanvas) {
            new Chart(categoryCanvas, {
                type: 'doughnut',
                data: {
                    labels: @json($categoryChart['labels']),
                    datasets: [{
                        data: @json($categoryChart['values']),
                        backgroundColor: @json($categoryChart['colors']),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    });
</script>
@endpush

@push('modals')
<!-- 1. Modal Rincian Total Koleksi -->
<div class="modal fade" id="koleksiBukuModal" tabindex="-1" aria-labelledby="koleksiBukuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="koleksiBukuModalLabel">
                    <i class="bi bi-journal-text me-2"></i>Rincian Stok Koleksi Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control modal-table-search" data-target-table="tableModalKoleksi" placeholder="Filter judul buku atau penulis...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableModalKoleksi">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Judul Buku</th>
                                <th class="text-center" style="width: 120px;">Stok Total</th>
                                <th class="text-center" style="width: 140px;">Stok Tersedia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booksList as $index => $book)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $book->title }}</div>
                                    <small class="text-muted">{{ $book->author }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light-secondary text-dark">{{ $book->total_stock }}</span>
                                </td>
                                <td class="text-center">
                                    @if($book->available_stock > 0)
                                        <span class="badge bg-light-success text-success">{{ $book->available_stock }} Tersedia</span>
                                    @else
                                        <span class="badge bg-light-danger text-danger">Habis</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada koleksi buku.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('books.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-gear me-1"></i> Kelola Data Buku
                </a>
                <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@if($canViewMemberStats)
<!-- 2. Modal Rincian Total Anggota -->
<div class="modal fade" id="anggotaModal" tabindex="-1" aria-labelledby="anggotaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white" id="anggotaModalLabel">
                    <i class="bi bi-people-fill me-2"></i>Daftar Anggota Peminjam ({{ $countAnggota }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control modal-table-search" data-target-table="tableModalAnggota" placeholder="Filter nama anggota...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableModalAnggota">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Nama Anggota</th>
                                @if($isStaff)
                                    <th>Email</th>
                                @endif
                                <th class="text-center" style="width: 140px;">Tgl Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anggotaList as $index => $anggota)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary me-2 flex-shrink-0">
                                            <span class="avatar-content text-uppercase fw-bold" style="font-size: 0.72rem;">
                                                {{ substr($anggota->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="fw-semibold text-dark">{{ $anggota->name }}</span>
                                    </div>
                                </td>
                                @if($isStaff)
                                    <td class="text-muted">{{ $anggota->email }}</td>
                                @endif
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($anggota->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isStaff ? 4 : 3 }}" class="text-center text-muted py-4">Belum ada anggota terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                @if($currentUser->role->canManageUsers())
                    <a href="{{ route('users.index') }}" class="btn btn-info text-white btn-sm">
                        <i class="bi bi-people me-1"></i> Manajemen User
                    </a>
                @endif
                <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- 3. Modal Rincian Buku Sedang Dipinjam -->
<div class="modal fade" id="bukuDipinjamModal" tabindex="-1" aria-labelledby="bukuDipinjamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white" id="bukuDipinjamModalLabel">
                    <i class="bi bi-arrow-left-right me-2"></i>Rincian Buku Sedang Dipinjam ({{ $countDipinjam }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control modal-table-search" data-target-table="tableModalDipinjam" placeholder="Filter judul buku atau nama peminjam...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableModalDipinjam">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Judul Buku</th>
                                <th>Peminjam</th>
                                <th class="text-center">Tgl Pinjam</th>
                                <th class="text-center">Batas Kembali</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dipinjamList as $index => $pinjam)
                            @php
                                $dueDate = $pinjam->due_date?->copy()->startOfDay() ?? now()->startOfDay();
                                $isLate = $pinjam->isOverdue();
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $pinjam->book->title ?? 'Buku Dihapus' }}</td>
                                <td>{{ $pinjam->member->name ?? 'Anggota Dihapus' }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($pinjam->borrow_date)->format('d M Y') }}</td>
                                <td class="text-center">
                                    <span class="{{ $isLate ? 'text-danger fw-bold' : '' }}">
                                        {{ $dueDate->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($isLate)
                                        <span class="badge bg-light-danger text-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-light-warning text-warning">Dipinjam</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada buku yang sedang dipinjam saat ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('borrowings.index') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-arrow-left-right me-1"></i> Kelola Sirkulasi
                </a>
                <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 4. Modal Rincian Total Denda -->
<div class="modal fade" id="totalDendaModal" tabindex="-1" aria-labelledby="totalDendaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="totalDendaModalLabel">
                    <i class="bi bi-cash-stack me-2"></i>Rincian Transaksi Terkena Denda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control modal-table-search" data-target-table="tableModalDenda" placeholder="Filter judul buku atau nama peminjam...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableModalDenda">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Judul Buku</th>
                                <th>Peminjam</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Nominal Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dendaList as $index => $denda)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $denda->book->title ?? 'Buku Dihapus' }}</td>
                                <td>{{ $denda->member->name ?? 'Anggota Dihapus' }}</td>
                                <td class="text-center">
                                    @if($denda->status === \App\Enums\BorrowingStatus::Returned)
                                        <span class="badge bg-light-success text-success">Dikembalikan</span>
                                    @elseif($denda->status === \App\Enums\BorrowingStatus::Lost)
                                        <span class="badge bg-light-dark text-dark">Hilang</span>
                                    @else
                                        <span class="badge bg-light-danger text-danger">Terlambat</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-light-danger text-danger fw-bold">
                                        Rp{{ number_format($denda->fine_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada catatan denda keterlambatan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="fw-bold text-danger">
                    Total Akumulasi: Rp{{ number_format($sumDenda, 0, ',', '.') }}
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('borrowings.index') }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-text me-1"></i> Lihat Sirkulasi
                    </a>
                    <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal-table-search').forEach(input => {
            input.addEventListener('keyup', function () {
                const keyword = this.value.toLowerCase();
                const tableId = this.dataset.targetTable;
                const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        });
    });
</script>
@endpush