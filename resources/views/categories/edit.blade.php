@extends('layouts.app')

@section('title', 'Edit Kategori - PerpusApp')
@section('page-title', 'Edit Data Kategori')

@section('content')
<div class="row">
    <div class="col-12 col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Edit Kategori</h4>
            </div>
            <div class="card-body">
                <form action="/categories/{{ $category->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/categories" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
