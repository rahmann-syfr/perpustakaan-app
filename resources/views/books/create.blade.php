@extends('layouts.app')

@section('title', 'Tambah Buku - PerpusApp')
@section('page-title', 'Tambah Data Buku Baru')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <!-- Atribut enctype wajib untuk upload file -->
                    <form action="/books" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Judul Buku</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kategori</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Penulis</label>
                                <input type="text" class="form-control" name="author" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>ISBN</label>
                                <input type="text" class="form-control" name="isbn" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tahun Terbit</label>
                                <input type="number" class="form-control" name="published_year" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Total Stok</label>
                                <input type="number" class="form-control" name="total_stock" required min="1">
                            </div>
                            
                            <!-- Input File Sampul Buku -->
                            <div class="col-md-12 mb-4">
                                <label>Foto Sampul Buku (Opsional)</label>
                                <input type="file" class="form-control" name="cover_image" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <a href="/books" class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection