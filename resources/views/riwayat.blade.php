@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - PerpusApp')
@section('page-title', 'Riwayat Peminjaman Saya')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Sampul</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowings as $row)
                                <tr>
                                    <td>
                                        @if($row->book && $row->book->cover_image)
                                            <img src="{{ asset('storage/' . $row->book->cover_image) }}" alt="Sampul" class="rounded" width="40" height="60" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex justify-content-center align-items-center" style="width: 40px; height: 60px;">
                                                <i class="bi bi-book text-secondary"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>{{ $row->book->title ?? 'Buku Dihapus' }}</strong></td>
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
                                            <span class="text-danger font-bold">Rp{{ number_format($row->fine_amount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-success">-</span>
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