@extends('layouts.app')

@section('title', 'Sirkulasi - PerpusApp')
@section('page-title', 'Data Sirkulasi Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Menampilkan pesan sukses atau error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible show fade">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible show fade">
                <i class="bi bi-exclamation-triangle"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Riwayat Peminjaman</h4>
                <a href="/borrowings/create" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Proses Pinjam Baru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($borrowings as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $row->user->name ?? '-' }}</strong><br>
                                        <small class="text-muted">Dilayani: {{ $row->admin->name ?? 'Sistem' }}</small>
                                    </td>
                                    <td>{{ $row->book->title ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->borrow_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->due_date)->format('d M Y') }}</td>
                                    <td>
                                        @if($row->status == 'borrowed')
                                            <span class="badge bg-warning">Dipinjam</span>
                                        @elseif($row->status == 'returned')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($row->status == 'overdue')
                                            <span class="badge bg-danger">Terlambat</span>
                                        @endif
                                    </td>
                                    <td>
                                        Rp{{ number_format($row->fine_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($row->status == 'borrowed')
                                            <!-- Tombol Kembalikan Buku -->
                                            <form action="/borrowings/{{ $row->id }}/return" method="POST" onsubmit="return confirm('Proses pengembalian buku ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check2-square"></i> Kembalikan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted"><i class="bi bi-check"></i></span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada transaksi sirkulasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection