<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sirkulasi Perpustakaan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center;}
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-danger { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN SIRKULASI PERPUSTAKAAN (PERPUSAPP)</h2>
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }} | Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
        @if(!empty($filters['filter']) || !empty($filters['start_date']) || !empty($filters['end_date']))
            <p style="font-size: 10px; margin-top: 3px;">
                @if(!empty($filters['filter']))
                    Periode: {{ ucfirst(str_replace('_', ' ', $filters['filter'])) }}
                @endif
                @if(!empty($filters['start_date']) && !empty($filters['end_date']))
                    ({{ \Carbon\Carbon::parse($filters['start_date'])->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($filters['end_date'])->format('d-m-Y') }})
                @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Peminjam</th>
                <th width="25%">Judul Buku</th>
                <th width="12%">Tgl Pinjam</th>
                <th width="12%">Batas Kembali</th>
                <th width="12%">Tgl Kembali</th>
                <th width="10%">Status</th>
                <th width="9%">Denda</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDenda = 0; @endphp
            @foreach ($borrowings as $index => $row)
                @php $totalDenda += $row->fine_amount; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->member->name ?? '-' }}</td>
                    <td>{{ $row->book->title ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->borrow_date)->format('d-m-Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->due_date)->format('d-m-Y') }}</td>
                    <td class="text-center">{{ $row->return_date ? \Carbon\Carbon::parse($row->return_date)->format('d-m-Y') : '-' }}</td>
                    <td class="text-center">
                        @if($row->status === \App\Enums\BorrowingStatus::Borrowed)
                            Dipinjam
                        @elseif($row->status === \App\Enums\BorrowingStatus::Returned)
                            Dikembalikan
                        @elseif($row->status === \App\Enums\BorrowingStatus::Overdue)
                            <span class="badge-danger">Terlambat</span>
                        @elseif($row->status === \App\Enums\BorrowingStatus::Lost)
                            <span class="badge-danger">Hilang</span>
                        @endif
                    </td>
                    <td class="text-right">Rp{{ number_format($row->fine_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" class="text-right">TOTAL PENDAPATAN DENDA</th>
                <th class="text-right">Rp{{ number_format($totalDenda, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>