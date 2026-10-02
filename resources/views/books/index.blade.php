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
                    <!-- id="table1" akan memicu DataTables bawaan template Mazer -->
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Sampul</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Tahun</th>
                                <th>Stok (Ada / Total)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($books as $book)
                                <tr>
                                    <td>
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Sampul" class="rounded" width="50" height="70" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex justify-content-center align-items-center" style="width: 50px; height: 70px;">
                                                <i class="bi bi-book text-secondary"></i>
                                            </div>
                                        @endif
                                    </td>
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
                                        <div class="d-flex gap-1">
                                            <!-- Tombol Edit (Kuning) -->
                                            <a href="/books/{{ $book->id }}/edit" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- Tombol Hapus (Merah) -->
                                            <form action="/books/{{ $book->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
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