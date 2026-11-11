@extends('layouts.app')

@section('title', 'Riwayat Saya - PerpusApp')
@section('page-title', 'Riwayat Transaksi Peminjaman')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Semua Transaksi Anda</h4>
                <p class="text-muted mb-0">Halaman ini memuat seluruh riwayat buku yang sedang dan pernah Anda pinjam.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Tanggal Dikembalikan</th>
                                <th>Status</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowings as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->book->title ?? 'Buku Dihapus' }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($item->borrow_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}</td>
                                    <td>
                                        {{ $item->return_date ? \Carbon\Carbon::parse($item->return_date)->format('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        @if($item->status == 'borrowed')
                                            <span class="badge bg-warning">Sedang Dipinjam</span>
                                        @elseif($item->status == 'returned')
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @elseif($item->status == 'overdue')
                                            <span class="badge bg-danger">Terlambat</span>
                                        @elseif($item->status == 'lost')
                                            <span class="badge bg-dark">Hilang</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->fine_amount > 0)
                                            <span class="text-danger fw-bold">Rp{{ number_format($item->fine_amount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-success">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pesan kosong ditaruh di luar tabel agar DataTables tidak error column count -->
                    @if($borrowings->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">Belum ada riwayat peminjaman.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection