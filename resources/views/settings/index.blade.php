@extends('layouts.app')

@section('title', 'Pengaturan Sistem - PerpusApp')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-sliders text-primary me-1"></i> Aturan Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="maksimal_hari_pinjam">Maksimal Durasi Pinjam (hari)</label>
                            <input type="number" min="1" max="90" class="form-control @error('maksimal_hari_pinjam') is-invalid @enderror"
                                   id="maksimal_hari_pinjam" name="maksimal_hari_pinjam"
                                   value="{{ old('maksimal_hari_pinjam', $settings['maksimal_hari_pinjam']) }}" required>
                            @error('maksimal_hari_pinjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="maksimal_buku_anggota">Maksimal Buku per Anggota</label>
                            <input type="number" min="1" max="50" class="form-control @error('maksimal_buku_anggota') is-invalid @enderror"
                                   id="maksimal_buku_anggota" name="maksimal_buku_anggota"
                                   value="{{ old('maksimal_buku_anggota', $settings['maksimal_buku_anggota']) }}" required>
                            @error('maksimal_buku_anggota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="hari_pengingat">Jadwal Pengingat (hari sebelum tenggat)</label>
                            <input type="number" min="0" max="30" class="form-control @error('hari_pengingat') is-invalid @enderror"
                                   id="hari_pengingat" name="hari_pengingat"
                                   value="{{ old('hari_pengingat', $settings['hari_pengingat']) }}" required>
                            @error('hari_pengingat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="maksimal_perpanjangan">Maksimal Perpanjangan (kali)</label>
                            <input type="number" min="0" max="10" class="form-control @error('maksimal_perpanjangan') is-invalid @enderror"
                                   id="maksimal_perpanjangan" name="maksimal_perpanjangan"
                                   value="{{ old('maksimal_perpanjangan', $settings['maksimal_perpanjangan']) }}" required>
                            @error('maksimal_perpanjangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="durasi_perpanjangan">Durasi Perpanjangan (hari)</label>
                            <input type="number" min="1" max="90" class="form-control @error('durasi_perpanjangan') is-invalid @enderror"
                                   id="durasi_perpanjangan" name="durasi_perpanjangan"
                                   value="{{ old('durasi_perpanjangan', $settings['durasi_perpanjangan']) }}" required>
                            @error('durasi_perpanjangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Pengingat jatuh tempo dikirim harian oleh scheduler
                        <code>app:send-due-reminders</code>.
                    </p>
                </div>
            </div>


            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-building text-info me-1"></i> Identitas Perpustakaan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="nama_perpustakaan">Nama Perpustakaan</label>
                            <input type="text" class="form-control @error('nama_perpustakaan') is-invalid @enderror"
                                   id="nama_perpustakaan" name="nama_perpustakaan"
                                   value="{{ old('nama_perpustakaan', $settings['nama_perpustakaan']) }}" required>
                            @error('nama_perpustakaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="email_perpustakaan">Email</label>
                            <input type="email" class="form-control @error('email_perpustakaan') is-invalid @enderror"
                                   id="email_perpustakaan" name="email_perpustakaan"
                                   value="{{ old('email_perpustakaan', $settings['email_perpustakaan']) }}">
                            @error('email_perpustakaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="alamat_perpustakaan">Alamat</label>
                            <input type="text" class="form-control" id="alamat_perpustakaan" name="alamat_perpustakaan"
                                   value="{{ old('alamat_perpustakaan', $settings['alamat_perpustakaan']) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="telepon_perpustakaan">Telepon</label>
                            <input type="text" class="form-control" id="telepon_perpustakaan" name="telepon_perpustakaan"
                                   value="{{ old('telepon_perpustakaan', $settings['telepon_perpustakaan']) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-light-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
