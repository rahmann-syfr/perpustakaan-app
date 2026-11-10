@extends('layouts.app')

@section('title', 'Edit User - PerpusApp')
@section('page-title', 'Edit Data Pengguna')

@section('content')
<div class="row">
    <div class="col-12 col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Edit User</h4>
            </div>
            <div class="card-body">
                <form action="/users/{{ $user->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Role / Hak Akses</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin" {{ (old('role') ?? $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="petugas" {{ (old('role') ?? $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                            <option value="anggota" {{ (old('role') ?? $user->role) == 'anggota' ? 'selected' : '' }}>Anggota</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/users" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Perbarui User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection