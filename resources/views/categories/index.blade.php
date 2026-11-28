@extends('layouts.app')

@section('title', 'Kategori Buku - PerpusApp')
@section('page-title', 'Manajemen Kategori')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h4 class="card-title mb-1">Daftar Kategori Buku</h4>
                    <p class="text-muted small mb-0">Kelola pengelompokan koleksi buku perpustakaan.</p>
                </div>
                @if(auth()->user()->role === \App\Enums\Role::Admin)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
                    </button>
                @endif
            </div>

            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="tableSearch" class="form-control border-start-0"
                                   placeholder="Cari nama kategori..." autocomplete="off">
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
                                <th style="width: 65px;">No</th>
                                <th>Nama Kategori</th>
                                @if(auth()->user()->role === \App\Enums\Role::Admin)
                                    <th class="text-center" style="width: 180px;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $index => $category)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-light-primary text-primary me-2">
                                            <i class="bi bi-tag-fill"></i>
                                        </span>
                                        <strong class="text-dark">{{ $category->name }}</strong>
                                    </td>
                                    @if(auth()->user()->role === \App\Enums\Role::Admin)
                                        <td class="text-center">
                                            <div class="table-action-buttons justify-content-center">
                                                <button type="button"
                                                        class="btn btn-sm btn-light-warning text-warning fw-semibold"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $category->id }}"
                                                        title="Edit Kategori">
                                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                                </button>

                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light-danger text-danger fw-semibold" title="Hapus Kategori">
                                                        <i class="bi bi-trash-fill me-1"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            @if($categories->isEmpty())
                                @include('partials.empty-row', [
                                    'colspan' => auth()->user()->role === \App\Enums\Role::Admin ? 3 : 2,
                                    'icon' => 'bi-tags',
                                    'title' => 'Belum Ada Kategori',
                                    'text' => auth()->user()->role === \App\Enums\Role::Admin
                                        ? 'Tambahkan kategori pertama untuk mulai mengelompokkan koleksi buku.'
                                        : 'Belum ada data kategori buku yang tersedia.',
                                ])
                            @endif
                        </tbody>
                    </table>

                    <div class="perpus-table-empty" id="tableEmpty" hidden>
                        <i class="bi bi-search"></i>
                        <p class="mb-0 fw-semibold">Kategori tidak ditemukan</p>
                        <small>Coba ubah kata kunci pencarian Anda.</small>
                    </div>
                </div>

                @if($categories->hasPages())
                    <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        {{ $categories->links() }}
                        <span class="perpus-table-counter small">
                            Menampilkan {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.table-search-script')

@if(auth()->user()->role === \App\Enums\Role::Admin)
@push('modals')
<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header perpus-modal-header">
                <h5 class="modal-title text-white" id="addCategoryModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" placeholder="Contoh: Pemrograman Web" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori (Ditempatkan di luar tabel agar valid & bebas bug backdrop) -->
@foreach ($categories as $category)
<div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header perpus-modal-header-warning">
                <h5 class="modal-title fw-bold" id="editModalLabel{{ $category->id }}">
                    <i class="bi bi-pencil-square me-2"></i>Edit Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endpush
@endif