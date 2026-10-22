@extends('layouts.app')

@section('title', 'Sirkulasi & Laporan - PerpusApp')
@section('page-title', 'Data Sirkulasi & Laporan')

@section('content')

<!-- KOTAK FILTER -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="bi bi-funnel-fill"></i> Filter Laporan</h4>
            </div>
            <div class="card-body">
                <form action="/borrowings" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="mb-2">Periode Cepat</label>
                        <select class="form-select" name="filter">
                            <option value="">Semua Waktu</option>
                            <option value="hari_ini" {{ request('filter') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="bulan_ini" {{ request('filter') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="tahun_ini" {{ request('filter') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-2">Dari Tanggal (Opsional)</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-2">Sampai Tanggal (Opsional)</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Terapkan</button>
                        <a href="/borrowings" class="btn btn-light-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TABEL DATA -->
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible show fade">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible show fade">
                <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Transaksi Peminjaman</h4>
                <div class="d-flex gap-2">
                    <a href="/borrowings/create" class="btn btn-success"><i class="bi bi-plus-circle"></i> Pinjamkan Buku</a>
                    <!-- Tombol Cetak PDF membawa parameter query string agar PDF terfilter -->
                    <a href="/borrowings/report-pdf?filter={{ request('filter') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Cetak Laporan PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowings as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->user->name ?? 'Terhapus' }}</td>
                                    <td>{{ $row->book->title ?? 'Terhapus' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->borrow_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->due_date)->format('d M Y') }}</td>
                                    <td>
                                        @if($row->status == 'borrowed') 
                                            <span class="badge bg-warning">Dipinjam</span>
                                        @elseif($row->status == 'returned') 
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @elseif($row->status == 'overdue') 
                                            <span class="badge bg-danger">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row->status == 'borrowed' || $row->status == 'overdue')
                                            <form action="/borrowings/{{ $row->id }}/return" method="POST" onsubmit="return confirm('Proses pengembalian buku ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">Kembalikan</button>
                                            </form>
                                        @else
                                            <span class="text-muted"><i class="bi bi-check2-all"></i> Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection