@extends('layouts.app')

@section('title', 'Katalog Buku - PerpusApp')
@section('page-title', 'Katalog Koleksi Buku')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="/katalog" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="search" placeholder="Cari judul buku atau nama penulis..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-5">
                        <select class="form-select" name="category">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @forelse ($books as $book)
        <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="card">
                <div class="card-content">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top img-fluid" alt="Sampul Buku" style="height: 300px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex justify-content-center align-items-center" style="height: 300px;">
                            <i class="bi bi-book text-secondary" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <p class="card-text text-muted mb-2"><i class="bi bi-person"></i> {{ $book->author }}</p>
                        <span class="badge bg-light-primary mb-3">{{ $book->category->name ?? 'Tanpa Kategori' }}</span>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-sm font-bold">Stok: 
                                <span class="{{ $book->available_stock > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $book->available_stock }}
                                </span>
                            </span>
                            @if($book->available_stock > 0)
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Habis</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <img src="{{ asset('assets/compiled/svg/error-404.svg') }}" width="200" alt="Tidak ada data">
            <h5 class="mt-3">Buku tidak ditemukan.</h5>
            <p class="text-muted">Cobalah menggunakan kata kunci pencarian yang lain.</p>
        </div>
    @endforelse
</div>
@endsection