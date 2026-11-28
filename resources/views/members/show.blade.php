@extends('layouts.app')

@section('title', 'Detail Anggota - PerpusApp')
@section('page-title', 'Detail Informasi Anggota')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="perpus-profile-hero">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-content text-uppercase">{{ substr($member->name, 0, 1) }}</span>
                </div>
                <h5 class="fw-bold mb-1">{{ $member->name }}</h5>
                <span class="badge bg-light-primary text-primary fw-semibold">Anggota</span>
            </div>
            <div class="card-body">
                <ul class="perpus-info-list">
                    <li>
                        <span class="label">Email</span>
                        <span class="value">{{ $member->email }}</span>
                    </li>
                    <li>
                        <span class="label">No HP</span>
                        <span class="value">{{ $member->phone ?: '-' }}</span>
                    </li>
                    <li>
                        <span class="label">Alamat</span>
                        <span class="value">{{ $member->address ?: '-' }}</span>
                    </li>
                    <li>
                        <span class="label">Bergabung</span>
                        <span class="value">{{ $member->created_at?->format('d M Y') ?? '-' }}</span>
                    </li>
                    <li>
                        <span class="label">Total Pinjam</span>
                        <span class="value">{{ $borrowings->count() }} transaksi</span>
                    </li>
                </ul>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit Data Anggota
                    </a>
                    <a href="{{ route('members.index') }}" class="btn btn-light-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <h4 class="card-title mb-1">Daftar Peminjaman</h4>
                    <p class="text-muted small mb-0">Semua transaksi peminjaman buku oleh anggota ini.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tenggat</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($borrowings as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->book->title ?? 'Buku Dihapus' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->borrow_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->status->badgeClass() }} fw-semibold">
                                            {{ $item->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-journal-x d-block fs-3 mb-2 opacity-50"></i>
                                        Anggota ini belum pernah meminjam buku.
                                    </td>
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
