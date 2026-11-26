@extends('layouts.app')

@section('title', 'Detail Buku - PerpusApp')
@section('page-title', 'Detail Informasi Buku')

@section('content')
<div class="row justify-content-center g-4">
    <div class="col-12 col-xl-8">
        <div class="card overflow-hidden mb-0">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h4 class="card-title mb-1">{{ $book->title }}</h4>
                    <p class="text-muted small mb-0">Informasi lengkap koleksi buku.</p>
                </div>
                <a href="{{ route('books.index') }}" class="btn btn-light-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4 align-items-start">
                    <div class="col-md-4 text-center">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/'.$book->cover_image) }}" class="img-fluid rounded-3 shadow-sm" alt="Sampul {{ $book->title }}" style="max-height: 360px; object-fit: cover;">
                        @else
                            <div class="book-cover-wrapper rounded-3 d-flex justify-content-center align-items-center mx-auto" style="max-width: 260px; height: 320px;">
                                <i class="bi bi-journal-richtext text-secondary opacity-50" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <ul class="perpus-info-list">
                            <li>
                                <span class="label">Kategori</span>
                                <span class="value">
                                    <span class="badge bg-light-primary text-primary fw-semibold">{{ $book->category->name ?? 'Tanpa Kategori' }}</span>
                                </span>
                            </li>
                            <li>
                                <span class="label">Penulis</span>
                                <span class="value">{{ $book->author }}</span>
                            </li>
                            <li>
                                <span class="label">Penerbit</span>
                                <span class="value">{{ $book->publisher ?? '-' }}</span>
                            </li>
                            <li>
                                <span class="label">ISBN</span>
                                <span class="value"><code class="text-dark">{{ $book->isbn }}</code></span>
                            </li>
                            <li>
                                <span class="label">Tahun Terbit</span>
                                <span class="value">{{ $book->published_year }}</span>
                            </li>
                            <li>
                                <span class="label">Total Stok</span>
                                <span class="value">{{ $book->total_stock }} eksemplar</span>
                            </li>
                            <li>
                                <span class="label">Stok Tersedia</span>
                                <span class="value">
                                    @if($book->available_stock > 0)
                                        <span class="badge bg-light-success text-success fw-semibold">{{ $book->available_stock }} tersedia</span>
                                    @else
                                        <span class="badge bg-light-danger text-danger fw-semibold">Habis</span>
                                    @endif
                                </span>
                            </li>
                        </ul>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-1"></i> Edit Buku
                            </a>
                            <a href="{{ route('books.index') }}" class="btn btn-light-secondary">Daftar Buku</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle text-primary me-1"></i> Info Peminjaman</h5>
            </div>
            <div class="card-body">
                <ul class="perpus-info-list mb-0">
                    <li>
                        <span class="label">Denda per hari</span>
                        <span class="value text-danger fw-semibold">Rp{{ number_format($book->fine_per_day ?? 0, 0, ',', '.') }}</span>
                    </li>
                    <li>
                        <span class="label">Total dipinjam</span>
                        <span class="value">{{ $book->borrowings()->count() }} kali</span>
                    </li>
                    <li>
                        <span class="label">Status</span>
                        <span class="value">
                            @if($book->available_stock > 0)
                                <span class="badge bg-light-success text-success">Tersedia</span>
                            @else
                                <span class="badge bg-light-danger text-danger">Habis</span>
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
