@extends('layouts.app')

@section('title', 'Dashboard - PerpusApp')
@section('page-title', 'Dashboard Perpustakaan')

@section('content')
<section class="row">
    <div class="col-12">
        
        <!-- Banner Selamat Datang (Terlihat oleh Semua Role) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl bg-primary me-4">
                                <span class="avatar-content text-uppercase fs-3">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="mb-1">Selamat datang kembali, {{ Auth::user()->name }}!</h4>
                                <p class="mb-0 text-muted">Anda login sebagai <span class="badge bg-light-primary">{{ ucfirst(Auth::user()->role) }}</span>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pemisahan Tampilan Berdasarkan Role -->
        @if(Auth::user()->role == 'anggota')
            
            <!-- TAMPILAN KHUSUS MAHASISWA / ANGGOTA -->
            <div class="row">
                <div class="col-6 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-book"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Sedang Dipinjam</h6>
                                    <h6 class="font-extrabold mb-0">{{ $pinjamanSaya }} Buku</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-8">
                    <div class="alert alert-light-primary color-primary h-100 d-flex flex-column justify-content-center">
                        <h4 class="alert-heading"><i class="bi bi-info-circle"></i> Mulai Membaca!</h4>
                        <p>Silakan telusuri koleksi buku terbaru kami atau cek tenggat waktu pengembalian buku di menu riwayat Anda.</p>
                        <div class="mt-2">
                            <a href="/katalog" class="btn btn-primary btn-sm me-2">Ke Katalog Buku</a>
                            <a href="/riwayat" class="btn btn-outline-primary btn-sm">Lihat Riwayat</a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            
            <!-- TAMPILAN KHUSUS ADMIN & SUPERADMIN -->
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon purple mb-2"><i class="bi bi-book-half"></i></div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Koleksi Buku</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalBuku }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2"><i class="bi bi-people-fill"></i></div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Anggota</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalAnggota }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2"><i class="bi bi-arrow-left-right"></i></div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Sedang Dipinjam</h6>
                                    <h6 class="font-extrabold mb-0">{{ $bukuDipinjam }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon red mb-2"><i class="bi bi-cash-stack"></i></div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pendapatan Denda</h6>
                                    <h6 class="font-extrabold mb-0">Rp{{ number_format($totalDenda, 0, ',', '.') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif

    </div>
</section>
@endsection