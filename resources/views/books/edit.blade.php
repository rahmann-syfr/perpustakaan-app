@extends('layouts.app')

@section('title', 'Edit Buku - PerpusApp')
@section('page-title', 'Ubah Data Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <!-- Form menggunakan method POST, tapi kita override menjadi PUT dengan @method('PUT') -->
                    <form action="/books/{{ $book->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Judul Buku</label>
                                <input type="text" class="form-control" name="title" value="{{ $book->title }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kategori</label>
                                <select class="form-select" name="category_id" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $book->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Penulis</label>
                                <input type="text" class="form-control" name="author" value="{{ $book->author }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>ISBN</label>
                                <input type="text" class="form-control" name="isbn" value="{{ $book->isbn }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tahun Terbit</label>
                                <input type="number" class="form-control" name="published_year" value="{{ $book->published_year }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Total Stok</label>
                                <input type="number" class="form-control" name="total_stock" value="{{ $book->total_stock }}" required min="1">
                                <small class="text-muted">Stok tersedia saat ini: {{ $book->available_stock }}</small>
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <label>Foto Sampul Buku (Opsional)</label>
                                <!-- Preview gambar lama jika ada -->
                                @if($book->cover_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Preview" class="rounded" width="100">
                                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar di atas.</div>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="cover_image" accept="image/*">
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <a href="/books" class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection