@extends('layouts.app')

@section('title', 'Laporan & Analitik - PerpusApp')
@section('page-title', 'Laporan & Analitik')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <p class="text-muted mb-0">Ringkasan performa perpustakaan, tren peminjaman, dan aktivitas anggota.</p>
    <div class="btn-group" role="group" aria-label="Rentang waktu">
        @foreach([3 => '3 Bulan', 6 => '6 Bulan', 12 => '12 Bulan'] as $value => $label)
            <a href="{{ route('analytics.index', ['range' => $value]) }}"
               class="btn btn-sm {{ $range === $value ? 'btn-primary' : 'btn-light-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Total Peminjaman</h6>
                <h4 class="font-extrabold mb-0">{{ $summary['total_borrowings'] }}</h4>
                <small class="text-muted">dalam {{ $range }} bulan terakhir</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Sedang Dipinjam</h6>
                <h4 class="font-extrabold mb-0 text-warning">{{ $summary['active_borrowings'] }}</h4>
                <small class="text-muted">buku aktif saat ini</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Akumulasi Denda</h6>
                <h4 class="font-extrabold mb-0 text-danger">Rp{{ number_format($summary['total_fine'], 0, ',', '.') }}</h4>
                <small class="text-muted">rata-rata Rp{{ number_format($summary['average_fine'], 0, ',', '.') }}/transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Total Koleksi</h6>
                <h4 class="font-extrabold mb-0 text-primary">{{ $summary['total_books'] }} <small class="fs-6">Judul</small></h4>
                <small class="text-muted">{{ $summary['total_members'] }} anggota terdaftar</small>
            </div>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up-arrow text-primary me-1"></i> Tren Peminjaman Bulanan</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 320px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart text-primary me-1"></i> Status Transaksi</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 320px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-tags text-primary me-1"></i> Distribusi per Kategori</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-star text-primary me-1"></i> Buku Terpopuler</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Judul Buku</th>
                                <th class="text-center">Dipinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topBooks as $index => $book)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $book->title }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light-primary text-primary">{{ $book->borrowings_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada data peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-people text-primary me-1"></i> Anggota Teraktif</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Anggota</th>
                                <th class="text-center">Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMembers as $index => $member)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $member->name }}</div>
                                        <small class="text-muted">{{ $member->email }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-primary text-primary">{{ $member->borrowings_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada aktivitas anggota.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/extensions/chart.js/chart.umd.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const primary = '#0d9488';
        const accent = '#6366f1';
        const gridColor = 'rgba(148, 163, 184, 0.18)';
        const textColor = '#64748b';

        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
        Chart.defaults.color = textColor;

        const monthlyCanvas = document.getElementById('monthlyChart');
        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'bar',
                data: {
                    labels: @json($monthlyChart['labels']),
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: @json($monthlyChart['values']),
                        backgroundColor: 'rgba(13, 148, 136, 0.75)',
                        hoverBackgroundColor: primary,
                        borderRadius: 8,
                        maxBarThickness: 46
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.92)',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false } },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            border: { display: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: @json($statusChart['labels']),
                    datasets: [{
                        data: @json($statusChart['values']),
                        backgroundColor: @json($statusChart['colors']),
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        const categoryCanvas = document.getElementById('categoryChart');
        if (categoryCanvas) {
            new Chart(categoryCanvas, {
                type: 'polarArea',
                data: {
                    labels: @json($categoryChart['labels']),
                    datasets: [{
                        data: @json($categoryChart['values']),
                        backgroundColor: @json($categoryChart['colors']).map(c => c + 'cc'),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    },
                    scales: {
                        r: {
                            grid: { color: gridColor },
                            ticks: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

