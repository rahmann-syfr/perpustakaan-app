@extends('layouts.app')

@section('title', 'Sirkulasi & Laporan - PerpusApp')
@section('page-title', 'Data Sirkulasi & Laporan')

@section('content')
@php
    // Statistik dihitung pada controller agar konsisten dengan filter aktif.
    $totalTransaksi = $stats['total'];
    $aktifDipinjam = $stats['dipinjam'];
    $terlambatCount = $stats['terlambat'];
    $selesaiCount = $stats['selesai'];
@endphp

<!-- Ringkasan Statistik Sirkulasi -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body px-3 px-md-4 py-3 d-flex align-items-center">
                <div class="stats-icon purple me-3 flex-shrink-0">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <h6 class="text-muted font-semibold mb-1">Total Transaksi</h6>
                    <h5 class="font-extrabold mb-0">{{ $totalTransaksi }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body px-3 px-md-4 py-3 d-flex align-items-center">
                <div class="stats-icon blue me-3 flex-shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <h6 class="text-muted font-semibold mb-1">Sedang Dipinjam</h6>
                    <h5 class="font-extrabold mb-0">{{ $aktifDipinjam }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body px-3 px-md-4 py-3 d-flex align-items-center">
                <div class="stats-icon red me-3 flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h6 class="text-muted font-semibold mb-1">Terlambat</h6>
                    <h5 class="font-extrabold mb-0 {{ $terlambatCount > 0 ? 'text-danger' : '' }}">{{ $terlambatCount }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body px-3 px-md-4 py-3 d-flex align-items-center">
                <div class="stats-icon green me-3 flex-shrink-0">
                    <i class="bi bi-check2-all"></i>
                </div>
                <div>
                    <h6 class="text-muted font-semibold mb-1">Dikembalikan</h6>
                    <h5 class="font-extrabold mb-0">{{ $selesaiCount }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Laporan -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header pb-2">
                <h5 class="card-title mb-0"><i class="bi bi-funnel-fill text-primary me-1"></i> Filter Periode Laporan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('borrowings.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold">Periode Cepat</label>
                        <select class="form-select" name="filter">
                            <option value="">Semua Waktu</option>
                            <option value="hari_ini" {{ request('filter') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="bulan_ini" {{ request('filter') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="tahun_ini" {{ request('filter') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Dari Tanggal</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Terapkan
                        </button>
                        <a href="{{ route('borrowings.index') }}" class="btn btn-light-secondary" data-bs-toggle="tooltip" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi Sirkulasi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h4 class="card-title mb-1">Daftar Transaksi Peminjaman</h4>
                    <p class="text-muted small mb-0">Kelola peminjaman aktif, pengembalian buku, dan cetak laporan PDF.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('borrowings.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Pinjamkan Buku
                    </a>
                    <a href="{{ route('borrowings.report', request()->query()) }}"
                       class="btn btn-danger"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
                    </a>
                    <a href="{{ route('borrowings.report-csv', request()->query()) }}"
                       class="btn btn-success">
                        <i class="bi bi-filetype-csv me-1"></i> CSV
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="tableSearch" class="form-control border-start-0"
                                   placeholder="Cari nama peminjam, judul buku, atau petugas..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-center justify-content-md-end">
                        <span class="perpus-table-counter" id="tableCounter"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table1">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Dicatat Oleh</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowings as $index => $row)
                                @php
                                    $dueDate = $row->due_date?->copy()->startOfDay() ?? now()->startOfDay();
                                    $isLate = $row->isOverdue();
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $row->member->name ?? 'Anggota Terhapus' }}</div>
                                        @if($row->member)
                                            <small class="text-muted">{{ $row->member->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $row->book->title ?? 'Buku Terhapus' }}</div>
                                        @if($row->fine_amount > 0)
                                            <small class="text-danger fw-bold">
                                                Denda: Rp{{ number_format($row->fine_amount, 0, ',', '.') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->admin)
                                            <div class="fw-semibold text-dark">{{ $row->admin->name }}</div>
                                            <span class="badge bg-light-info text-info" style="font-size: 0.7rem;">
                                                {{ ucfirst($row->admin->role->value ?? $row->admin->role) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light-secondary text-secondary" style="font-size: 0.75rem;">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($row->borrow_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="{{ $isLate ? 'text-danger fw-bold' : '' }}">
                                            {{ $dueDate->format('d M Y') }}
                                        </span>
                                        @if($isLate)
                                            <div class="text-danger small">Lewat tenggat</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row->status === \App\Enums\BorrowingStatus::Borrowed)
                                            <span class="badge bg-light-warning text-warning fw-semibold">
                                                <i class="bi bi-hourglass-split me-1"></i>Dipinjam
                                            </span>
                                        @elseif($row->status === \App\Enums\BorrowingStatus::Returned)
                                            <span class="badge bg-light-success text-success fw-semibold">
                                                <i class="bi bi-check-circle-fill me-1"></i>Dikembalikan
                                            </span>
                                        @elseif($row->status === \App\Enums\BorrowingStatus::Overdue)
                                            <span class="badge bg-light-danger text-danger fw-semibold">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat
                                            </span>
                                        @elseif($row->status === \App\Enums\BorrowingStatus::Lost)
                                            <span class="badge bg-light-dark text-dark fw-semibold">
                                                <i class="bi bi-x-octagon-fill me-1"></i>Hilang
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row->status->isActive())
                                            <div class="d-flex flex-column flex-sm-row gap-1 justify-content-center">
                                                @if(! $row->isOverdue())
                                                    <button type="button"
                                                            class="btn btn-sm btn-light-warning fw-semibold"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#extendModal{{ $row->id }}">
                                                        <i class="bi bi-calendar-plus me-1"></i>Perpanjang
                                                    </button>
                                                @endif
                                                @if($row->hasOutstandingFine())
                                                    <a href="{{ route('fines.create', $row) }}" class="btn btn-sm btn-light-danger fw-semibold">
                                                        <i class="bi bi-cash-coin me-1"></i>Bayar Denda
                                                    </a>
                                                @endif
                                                <button type="button"
                                                        class="btn btn-sm btn-primary fw-semibold"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#returnModal{{ $row->id }}">
                                                    <i class="bi bi-box-arrow-in-down-left me-1"></i>Kembalikan
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-success small fw-semibold">
                                                <i class="bi bi-check2-all me-1"></i>Selesai
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            @if($borrowings->isEmpty())
                                @include('partials.empty-row', [
                                    'colspan' => 8,
                                    'icon' => 'bi-journal-x',
                                    'title' => 'Tidak Ada Data Transaksi',
                                    'text' => 'Belum ada transaksi peminjaman pada periode yang dipilih.',
                                    'actionUrl' => route('borrowings.create'),
                                    'actionLabel' => 'Catat Peminjaman Baru',
                                    'actionIcon' => 'bi-plus-circle',
                                ])
                            @endif
                        </tbody>
                    </table>

                    <div class="perpus-table-empty" id="tableEmpty" hidden>
                        <i class="bi bi-search"></i>
                        <p class="mb-0 fw-semibold">Transaksi tidak ditemukan</p>
                        <small>Coba ubah kata kunci pencarian Anda.</small>
                    </div>
                </div>
                <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                    {{ $borrowings->links() }}
                    <span class="perpus-table-counter small">
                        Menampilkan {{ $borrowings->firstItem() ?? 0 }}-{{ $borrowings->lastItem() ?? 0 }} dari {{ $borrowings->total() }} transaksi
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
@foreach ($borrowings as $row)
    @if($row->status->isActive())
        <!-- Modal Pengembalian Buku -->
        <div class="modal fade" id="returnModal{{ $row->id }}" tabindex="-1" aria-labelledby="returnModalLabel{{ $row->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header perpus-modal-header">
                        <h5 class="modal-title text-white fw-bold" id="returnModalLabel{{ $row->id }}">
                            <i class="bi bi-box-arrow-in-down-left me-2"></i>Proses Pengembalian Buku
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('borrowings.return', $row) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="mb-3 small text-muted">
                                Peminjam: <strong class="text-dark">{{ $row->member->name ?? '-' }}</strong><br>
                                Buku: <strong class="text-dark">{{ $row->book->title ?? '-' }}</strong><br>
                                Batas Kembali: <strong class="{{ $row->isOverdue() ? 'text-danger' : 'text-dark' }}">{{ $row->due_date?->format('d M Y') }}</strong>
                            </p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Pengembalian <span class="text-danger">*</span></label>
                                <select name="status"
                                        class="form-select"
                                        required
                                        onchange="document.getElementById('lostFineBox{{ $row->id }}').hidden = (this.value !== '{{ \App\Enums\BorrowingStatus::Lost->value }}')">
                                    <option value="{{ \App\Enums\BorrowingStatus::Returned->value }}" selected>Dikembalikan (Normal)</option>
                                    <option value="{{ \App\Enums\BorrowingStatus::Lost->value }}">Buku Hilang</option>
                                </select>
                                <small class="text-muted">Denda keterlambatan akan dihitung otomatis oleh sistem jika melewati batas kembali.</small>
                            </div>
                            <div class="mb-2" id="lostFineBox{{ $row->id }}" hidden>
                                <label class="form-label fw-semibold">Denda Penggantian Buku Hilang (Rp)</label>
                                <input type="number" name="lost_fine_amount" class="form-control" min="0" placeholder="Contoh: 50000">
                                <small class="text-muted">Kosongkan untuk menggunakan nominal denda buku hilang default sistem.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Simpan Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Perpanjangan Peminjaman -->
        @if(! $row->isOverdue())
            <div class="modal fade" id="extendModal{{ $row->id }}" tabindex="-1" aria-labelledby="extendModalLabel{{ $row->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header perpus-modal-header-warning">
                            <h5 class="modal-title fw-bold" id="extendModalLabel{{ $row->id }}">
                                <i class="bi bi-calendar-plus me-2"></i>Perpanjang Masa Peminjaman
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('borrowings.extend', $row) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <p class="mb-2 small text-muted">
                                    Peminjam: <strong class="text-dark">{{ $row->member->name ?? '-' }}</strong><br>
                                    Buku: <strong class="text-dark">{{ $row->book->title ?? '-' }}</strong><br>
                                    Batas Kembali Saat Ini: <strong class="text-danger">{{ $row->due_date?->format('d M Y') }}</strong>
                                </p>
                                <div class="mt-3">
                                    <label class="form-label fw-semibold">Tambah Durasi (Hari) <span class="text-danger">*</span></label>
                                    <input type="number" name="days" class="form-control" min="1" max="30" value="7" required>
                                    <small class="text-muted">Masukkan jumlah hari tambahan dari batas kembali saat ini (minimal 1 hari).</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning btn-sm fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i> Simpan Perpanjangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endforeach
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Perpus.tableSearch({
            input: '#tableSearch',
            table: '#table1',
            empty: '#tableEmpty',
            counter: '#tableCounter',
            columns: [1, 2, 3],
        });
    });
</script>
@endpush