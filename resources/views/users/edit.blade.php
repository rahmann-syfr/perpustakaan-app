@extends('layouts.app')

@section('title', 'Edit Anggota - PerpusApp')
@section('page-title', 'Ubah Data Anggota')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form action="/users/{{ $user->id }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label>Password Baru (Opsional)</label>
                                <input type="password" class="form-control" name="password" minlength="8">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <a href="/users" class="btn btn-light-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection