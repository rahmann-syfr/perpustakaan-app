@extends('layouts.app')

@section('title', 'Tambah Buku - PerpusApp')
@section('page-title', 'Tambah Data Buku')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Tambah Buku Baru</h4>
            </div>
            <div class="card-body">
                <form action="/books" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Judul Buku</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penulis</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penerbit</label>
                            <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Terbit</label>
                            <!-- Batasan Tahun: Min 1900, Max Tahun Ini -->
                            <input type="number" name="published_year" class="form-control" value="{{ old('published_year') }}" min="1900" max="{{ date('Y') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Stok</label>
                            <!-- Batasan Stok: Min 1, Max 999 untuk keamanan DB -->
                            <input type="number" name="total_stock" class="form-control" value="{{ old('total_stock', 1) }}" min="1" max="999" required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label">Cover Buku (Opsional)</label>
                            <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB.</small>
                            @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/books" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection