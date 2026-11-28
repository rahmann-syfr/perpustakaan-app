@extends('layouts.app')

@section('title', 'Data User - PerpusApp')
@section('page-title', 'Manajemen User')

@section('content')
@php
    $currentUser = Auth::user();
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h4 class="card-title mb-1">Daftar Pengguna Sistem</h4>
                    <p class="text-muted small mb-0">
                        Kelola akun staf perpustakaan (Admin dan Petugas).
                    </p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah User
                </a>
            </div>

            <div class="card-body">
                @if($users->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Belum Ada Pengguna</h5>
                        <p class="text-muted mb-3">Tambahkan akun pengguna baru ke dalam sistem.</p>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah User
                        </a>
                    </div>
                @else
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="tableSearch" class="form-control border-start-0"
                                       placeholder="Cari nama atau email pengguna..." autocomplete="off">
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
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th class="text-center">Role / Hak Akses</th>
                                    <th>Tgl Terdaftar</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    @php
                                        // Otoritas dihitung oleh policy agar konsisten dengan controller.
                                        $canEdit = $currentUser->can('update', $user);
                                        $canDelete = $currentUser->can('delete', $user);
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary me-2 flex-shrink-0">
                                                    <span class="avatar-content text-uppercase fw-bold" style="font-size: 0.75rem;">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                                    @if($currentUser->id === $user->id)
                                                        <span class="badge bg-light-info text-info ms-1" style="font-size: 0.68rem;">Akun Anda</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $user->email }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $user->role->badgeClass() }} fw-semibold">
                                                {{ $user->role->label() }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            @if($canEdit || $canDelete)
                                                <div class="table-action-buttons justify-content-center">
                                                    @if($canEdit)
                                                        <a href="{{ route('users.edit', $user) }}"
                                                           class="btn btn-sm btn-light-warning text-warning"
                                                           data-bs-toggle="tooltip"
                                                           title="Edit User">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    @endif

                                                    @if($canDelete)
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="form-delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-light-danger text-danger"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Hapus User">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-light-secondary text-muted fw-normal">
                                                    <i class="bi bi-lock-fill me-1"></i>Dilindungi
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="perpus-table-empty" id="tableEmpty" hidden>
                            <i class="bi bi-search"></i>
                            <p class="mb-0 fw-semibold">Pengguna tidak ditemukan</p>
                            <small>Coba ubah kata kunci pencarian Anda.</small>
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        {{ $users->links() }}
                        <span class="perpus-table-counter small">
                            Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.table-search-script')