@extends('layouts.app')

@section('title', 'Manajemen Anggota - PerpusApp')
@section('page-title', 'Data Anggota Perpustakaan')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Total Anggota</h6>
                <h4 class="font-extrabold mb-0">{{ $summary['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Sedang Meminjam</h6>
                <h4 class="font-extrabold mb-0 text-success">{{ $summary['active'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Memiliki Tunggakan</h6>
                <h4 class="font-extrabold mb-0 text-danger">{{ $summary['overdue'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body">
                <h6 class="text-muted small mb-1">Ditampilkan</h6>
                <h4 class="font-extrabold mb-0 text-primary">{{ $members->total() }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h4 class="card-title mb-1">Daftar Anggota</h4>
                    <p class="text-muted small mb-0">Kelola profil dan akun anggota perpustakaan.</p>
                </div>
                <a href="{{ route('members.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('members.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" id="tableSearch" class="form-control border-start-0"
                                   placeholder="Cari nama atau email anggota..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-7 col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Meminjam</option>
                            <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Tidak Meminjam</option>
                        </select>
                    </div>
                    <div class="col-5 col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filter</button>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-center justify-content-md-end">
                        @if(request('search') || request('status'))
                            <a href="{{ route('members.index') }}" class="btn btn-light-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        @else
                            <span class="perpus-table-counter" id="tableCounter"></span>
                        @endif
                    </div>
                </form>
                @if($members->isEmpty())
                    <div class="text-center py-5">
                        @if(request('search') || request('status'))
                            <div class="bg-light-secondary text-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                                <i class="bi bi-search fs-1"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Tidak Ada Anggota yang Cocok</h5>
                            <p class="text-muted mb-3">Ubah kata kunci pencarian atau reset filter untuk melihat daftar lengkap.</p>
                            <a href="{{ route('members.index') }}" class="btn btn-light-secondary btn-sm me-1">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                            </a>
                            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-plus me-1"></i> Tambah Anggota
                            </a>
                        @else
                            <div class="bg-light-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                                <i class="bi bi-person-badge fs-1"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Belum Ada Anggota</h5>
                            <p class="text-muted mb-3">Tambahkan anggota pertama untuk mulai layanan peminjaman.</p>
                            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-plus me-1"></i> Tambah Anggota
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table1">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 55px;">No</th>
                                    <th>Nama Anggota</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th class="text-center">Aktif</th>
                                    <th>Tgl Terdaftar</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $index => $member)
                                    <tr>
                                        <td class="text-muted">{{ $members->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm flex-shrink-0">
                                                    <span class="avatar-content text-uppercase">{{ substr($member->name, 0, 1) }}</span>
                                                </div>
                                                <strong class="text-dark">{{ $member->name }}</strong>
                                            </div>
                                        </td>
                                        <td><span class="text-muted">{{ $member->email }}</span></td>
                                        <td><span class="text-muted">{{ $member->phone ?: '-' }}</span></td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary">{{ $member->active_borrowings_count }} aktif</span>
                                        </td>
                                        <td>{{ $member->created_at?->format('d M Y') ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="table-action-buttons justify-content-center">
                                                <a href="{{ route('members.show', $member->id) }}" class="btn btn-sm btn-light-primary text-primary" data-bs-toggle="tooltip" title="Detail">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                <a href="{{ route('members.edit', $member->id) }}" class="btn btn-sm btn-light-warning text-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light-danger text-danger" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="perpus-table-empty" id="tableEmpty" hidden>
                            <i class="bi bi-search"></i>
                            <p class="mb-0 fw-semibold">Tidak ada anggota yang cocok</p>
                            <small>Coba ubah kata kunci pencarian Anda.</small>
                        </div>
                    </div>
                @endif

                @if($members->hasPages())
                    <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        {{ $members->links() }}
                        <span class="perpus-table-counter small">
                            Menampilkan {{ $members->firstItem() ?? 0 }}-{{ $members->lastItem() ?? 0 }} dari {{ $members->total() }} anggota
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Perpus.tableSearch({
            input: '#tableSearch',
            table: '#table1',
            empty: '#tableEmpty',
            counter: '#tableCounter',
            columns: [1, 2],
        });
    });
</script>
@endpush