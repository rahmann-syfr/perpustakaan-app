@extends('layouts.app')

@section('title', 'Data Buku - PerpusApp')
@section('page-title', 'Manajemen Data Buku')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible show fade">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Koleksi Buku</h4>
                <a href="/books/create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Buku</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Sampul</th>
                                <th>Judul & Penulis</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Stok Total</th>
                                <th>Stok Tersedia</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($books as $book)
                                <tr>
                                    <td>
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Sampul" class="rounded" width="40" height="60" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex justify-content-center align-items-center" style="width: 40px; height: 60px;">
                                                <i class="bi bi-book text-secondary"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $book->title }}</strong><br>
                                        <small class="text-muted">{{ $book->author }} (ISBN: {{ $book->isbn }})</small>
                                    </td>
                                    <td><span class="badge bg-light-primary">{{ $book->category->name ?? '-' }}</span></td>
                                    <td>{{ $book->published_year }}</td>
                                    <td>{{ $book->total_stock }}</td>
                                    <td>
                                        @if($book->available_stock > 0)
                                            <span class="badge bg-success">{{ $book->available_stock }} Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Habis</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="/books/{{ $book->id }}/edit" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                            <form action="/books/{{ $book->id }}" method="POST" onsubmit="return confirm('Hapus buku ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection