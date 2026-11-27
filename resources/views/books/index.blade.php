@extends('layouts.app')

@section('title', 'Data Buku - PerpusApp')
@section('page-title', 'Manajemen Data Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h4 class="card-title mb-1">Daftar Koleksi Buku</h4>
                    <p class="text-muted small mb-0">Kelola stok, sampul, dan informasi bibliografi koleksi perpustakaan.</p>
                </div>
                <a href="{{ route('books.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Buku
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('books.index') }}" method="GET" class="row g-2 mb-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="tableSearch" name="search" class="form-control border-start-0"
                                   value="{{ request('search') }}"
                                   placeholder="Cari judul, penulis, atau ISBN..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if(request('search') || request('category'))
                            <a href="{{ route('books.index') }}" class="btn btn-light-secondary" data-bs-toggle="tooltip" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table1">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 65px;">Sampul</th>
                                <th>Judul & Penulis</th>
                                <th>Kategori</th>
                                <th class="text-center">Tahun</th>
                                <th class="text-center">Stok Total</th>
                                <th class="text-center">Stok Tersedia</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                            <tbody>
                                @foreach ($books as $book)
                                    <tr>
                                        <td>
                                            @if($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                     alt="Sampul {{ $book->title }}"
                                                     class="rounded shadow-sm"
                                                     width="44"
                                                     height="62"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-light border rounded d-flex justify-content-center align-items-center text-secondary" style="width: 44px; height: 62px;">
                                                    <i class="bi bi-journal-richtext fs-5 opacity-50"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $book->title }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>{{ $book->author }}
                                                @if($book->isbn)
                                                    &bull; ISBN: {{ $book->isbn }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary fw-semibold">
                                                {{ $book->category->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $book->published_year }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light-secondary text-dark">{{ $book->total_stock }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($book->available_stock > 0)
                                                <span class="badge bg-light-success text-success fw-semibold">
                                                    {{ $book->available_stock }} Tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-light-danger text-danger fw-semibold">Habis</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="table-action-buttons justify-content-center">
                                                <a href="{{ route('books.edit', $book) }}"
                                                   class="btn btn-sm btn-light-warning text-warning"
                                                   data-bs-toggle="tooltip"
                                                   title="Edit Buku">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-light-danger text-danger"
                                                            data-bs-toggle="tooltip"
                                                            title="Hapus Buku">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($books->isEmpty())
                                    @include('partials.empty-row', [
                                        'colspan' => 7,
                                        'icon' => 'bi-book',
                                        'title' => 'Belum Ada Koleksi Buku',
                                        'text' => 'Mulai tambahkan data buku baru ke dalam sistem perpustakaan.',
                                        'actionUrl' => route('books.create'),
                                        'actionLabel' => 'Tambah Buku Pertama',
                                        'actionIcon' => 'bi-plus-circle',
                                    ])
                                @endif
                            </tbody>
                        </table>

                        <div class="perpus-table-empty" id="tableEmpty" hidden>
                            <i class="bi bi-search"></i>
                            <p class="mb-0 fw-semibold">Buku tidak ditemukan</p>
                            <small>Coba ubah kata kunci pencarian Anda.</small>
                        </div>
                    </div>

                    @if($books->hasPages())
                        <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                            {{ $books->links() }}
                            <span class="perpus-table-counter small">
                                Menampilkan {{ $books->firstItem() ?? 0 }}-{{ $books->lastItem() ?? 0 }} dari {{ $books->total() }} buku
                            </span>
                        </div>
                    @endif
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.table-search-script')