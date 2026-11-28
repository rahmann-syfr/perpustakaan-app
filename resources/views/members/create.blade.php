@extends('layouts.app')

@section('title', 'Tambah Anggota - PerpusApp')
@section('page-title', 'Tambah Anggota Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
                <div>
                    <h4 class="card-title mb-1">Form Tambah Anggota</h4>
                    <p class="text-muted small mb-0">Lengkapi biodata anggota perpustakaan.</p>
                </div>
                <a href="{{ route('members.index') }}" class="btn btn-light-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('members.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label fw-semibold">No HP <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="081234567890" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" rows="3" maxlength="500" class="form-control @error('address') is-invalid @enderror" placeholder="Alamat lengkap anggota" required>{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('members.index') }}" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Anggota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
