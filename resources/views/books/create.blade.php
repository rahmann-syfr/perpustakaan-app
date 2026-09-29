@extends('layouts.app')

@section('title', 'Tambah Buku - PerpusApp')
@section('page-title', 'Tambah Data Buku')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Tambah Buku Baru</h4>
            </div>
            <div class="card-body">
                <!-- Menampilkan Error Validasi -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/books" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="title">Judul Buku</label>
                        <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="author">Penulis</label>
                            <input type="text" id="author" name="author" class="form-control" value="{{ old('author') }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="isbn">ISBN</label>
                            <input type="text" id="isbn" name="isbn" class="form-control" value="{{ old('isbn') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="published_year">Tahun Terbit</label>
                            <input type="number" id="published_year" name="published_year" class="form-control" value="{{ old('published_year') }}" min="1900" max="2100" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="total_stock">Total Stok</label>
                            <input type="number" id="total_stock" name="total_stock" class="form-control" value="{{ old('total_stock') }}" min="1" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="/books" class="btn btn-light-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection