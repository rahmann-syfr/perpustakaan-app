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
            
            <!-- TAMPILAN KHUSUS ADMIN & PETUGAS -->
            <div class="row">
                <!-- REVISI DOSEN: Card ini sekarang bisa diklik untuk memunculkan popup -->
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card" data-bs-toggle="modal" data-bs-target="#koleksiBukuModal" style="cursor: pointer; transition: 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" title="Klik untuk lihat rincian stok">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon purple mb-2"><i class="bi bi-book-half"></i></div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Koleksi</h6>
                                    <!-- Menampilkan jumlah Judul Buku -->
                                    <h6 class="font-extrabold mb-0">{{ $totalBuku }} Judul</h6> 
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
                                    <span class="badge bg-light-secondary text-dark mt-2" style="font-size: 0.7rem;">Khusus Peminjam</span>
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
                                    <h6 class="text-muted font-semibold">Buku Dipinjam</h6>
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
                                    <div class="stats-icon red mb-2"><i class="bi bi-cash"></i></div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Denda</h6>
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

<!-- Modal Popup Detail Koleksi Buku (Khusus Admin/Petugas) -->
@if(Auth::user()->role != 'anggota')
<div class="modal fade" id="koleksiBukuModal" tabindex="-1" aria-labelledby="koleksiBukuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="koleksiBukuModalLabel"><i class="bi bi-journal-text"></i> Rincian Stok Koleksi Buku</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th class="text-center">Stok Total</th>
                                <th class="text-center">Stok Tersedia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booksData as $index => $book)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $book->title }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $book->total_stock }}</span></td>
                                <td class="text-center">
                                    @if($book->available_stock > 0)
                                        <span class="badge bg-success">{{ $book->available_stock }}</span>
                                    @else
                                        <span class="badge bg-danger">Habis</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada koleksi buku.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection