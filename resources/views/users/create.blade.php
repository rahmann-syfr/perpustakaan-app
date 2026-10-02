@extends('layouts.app')

@section('title', 'Tambah Anggota - PerpusApp')
@section('page-title', 'Tambah Anggota Baru')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form action="/users" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" placeholder="contoh@mahasiswa.unsika.ac.id" required>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Masukkan password" required minlength="8">
                                <small class="text-muted">Minimal 8 karakter.</small>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <a href="/users" class="btn btn-light-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Anggota</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection