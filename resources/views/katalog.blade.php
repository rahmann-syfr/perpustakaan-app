@extends('layouts.app')

@section('title', 'Katalog Buku - PerpusApp')
@section('page-title', 'Katalog Buku Perpustakaan')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <p class="text-muted">Jelajahi koleksi buku terbaru kami. Hubungi petugas perpustakaan untuk melakukan peminjaman.</p>
    </div>

    @forelse ($books as $book)
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-content">
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
                Belum ada buku di perpustakaan.
            </div>
        </div>
    @endforelse
</div>
@endsection