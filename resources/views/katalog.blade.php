@extends('layouts.app')

@section('title', 'Katalog Buku - PerpusApp')
@section('page-title', 'Katalog Buku Perpustakaan')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <p class="text-muted">Jelajahi koleksi buku terbaru kami. Gunakan kotak pencarian di bawah untuk menemukan buku dengan cepat.</p>
        
        <div class="card">
            <div class="card-body py-3">
                <form action="/katalog" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari judul atau penulis..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <select name="category" class="form-select">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @forelse ($books as $book)
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-content">
                    
                    <!-- Menampilkan Cover Buku -->
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top img-fluid" alt="Cover {{ $book->title }}" style="height: 300px; object-fit: cover;">
                    @else
                        <!-- Placeholder jika tidak ada cover -->
                        <div class="bg-light d-flex justify-content-center align-items-center" style="height: 300px;">
                            <i class="bi bi-book text-secondary" style="font-size: 5rem;"></i>
                        </div>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <h6 class="card-subtitle mb-3 text-muted">{{ $book->author }} ({{ $book->published_year }})</h6>
                        
                        <div class="mb-2">
                            <span class="badge bg-light-primary">{{ $book->category->name ?? 'Tanpa Kategori' }}</span>
                        </div>
                        
                        <p class="card-text mb-1">
                            <small>ISBN: {{ $book->isbn }}</small>
                        </p>
                        
                        @if($book->available_stock > 0)
                            <span class="badge bg-success">Tersedia: {{ $book->available_stock }} Buku</span>
                        @else
                            <span class="badge bg-danger">Stok Habis</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light-warning">
                Buku yang Anda cari tidak ditemukan.
            </div>
        </div>
    @endforelse
</div>
@endsection