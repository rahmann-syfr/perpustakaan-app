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
                <h4 class="card-title">Daftar Buku</h4>
                <a href="/books/create" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Buku
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Tahun</th>
                                <th>Stok (Tersedia / Total)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($books as $index => $book)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $book->title }}</strong><br>
                                        <small class="text-muted">ISBN: {{ $book->isbn }}</small>
                                    </td>
                                    <td>{{ $book->category->name ?? '-' }}</td>
                                    <td>{{ $book->author }}</td>
                                    <td>{{ $book->published_year }}</td>
                                    <td>
                                        <span class="badge {{ $book->available_stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $book->available_stock }} / {{ $book->total_stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="/books/{{ $book->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data buku.</td>
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