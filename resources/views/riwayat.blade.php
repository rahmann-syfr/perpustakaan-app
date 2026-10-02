@extends('layouts.app')

@section('title', 'Riwayat Saya - PerpusApp')
@section('page-title', 'Riwayat Peminjaman Saya')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <p class="text-muted mb-4">Pantau buku yang sedang Anda pinjam dan riwayat pengembalian Anda di sini.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Status</th>
                                    <th>Denda</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayat as $row)
                                    <tr>
                                        <td class="text-bold-500">{{ $row->book->title ?? 'Buku Dihapus' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->borrow_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->due_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($row->status == 'borrowed')
                                                <span class="badge bg-warning">Sedang Dipinjam</span>
                                            @elseif($row->status == 'returned')
                                                <span class="badge bg-success">Dikembalikan</span>
                                            @elseif($row->status == 'overdue')
                                                <span class="badge bg-danger">Terlambat</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->fine_amount > 0)
                                                <span class="text-danger">Rp{{ number_format($row->fine_amount, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->admin->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                            Anda belum memiliki riwayat peminjaman buku.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsections