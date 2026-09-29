@extends('layouts.app')

@section('title', 'Dashboard - PerpusApp')
@section('page-title', 'Dashboard Statistik')

@section('content')
<div class="row">
    <!-- Card Total Buku -->
    <div class="col-12 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start">
                        <div class="stats-icon blue mb-2">
                            <i class="bi bi-book-fill" style="color: #fff;"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-8">
                        <h6 class="text-muted font-semibold">Total Stok Buku</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalBuku }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Anggota -->
    <div class="col-12 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start">
                        <div class="stats-icon green mb-2">
                            <i class="bi bi-people-fill" style="color: #fff;"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-8">
                        <h6 class="text-muted font-semibold">Total Anggota</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalAnggota }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Buku Dipinjam -->
    <div class="col-12 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start">
                        <div class="stats-icon red mb-2">
                            <i class="bi bi-arrow-left-right" style="color: #fff;"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-8">
                        <h6 class="text-muted font-semibold">Sedang Dipinjam</h6>
                        <h6 class="font-extrabold mb-0">{{ $bukuDipinjam }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection