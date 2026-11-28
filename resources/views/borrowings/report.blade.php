@php
    $namaPerpus = \App\Models\SystemSetting::get('nama_perpustakaan') ?: 'PerpusApp';
    $alamatPerpus = \App\Models\SystemSetting::get('alamat_perpustakaan');
    $emailPerpus = \App\Models\SystemSetting::get('email_perpustakaan');
    $teleponPerpus = \App\Models\SystemSetting::get('telepon_perpustakaan');
    $logoSetting = \App\Models\SystemSetting::get('logo_perpustakaan');

    $logoBase64 = null;
    if ($logoSetting) {
        $logoFullPath = public_path('storage/' . ltrim($logoSetting, '/'));
        if (is_file($logoFullPath)) {
            $ext = strtolower(pathinfo($logoFullPath, PATHINFO_EXTENSION)) ?: 'png';
            $mime = $ext === 'jpg' ? 'jpeg' : $ext;
            $logoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoFullPath));
        }
    }

    $user = \Illuminate\Support\Facades\Auth::user();
    $printerName = $user?->name ?? 'Admin';
    $printerRole = $user?->role?->label() ?? 'Petugas';

    $totalCount = $borrowings->count();
    $countBorrowed = $borrowings->where('status', \App\Enums\BorrowingStatus::Borrowed)->count();
    $countReturned = $borrowings->where('status', \App\Enums\BorrowingStatus::Returned)->count();
    $countOverdue = $borrowings->filter(fn ($b) => $b->status === \App\Enums\BorrowingStatus::Overdue || $b->isOverdue())->count();
    $countLost = $borrowings->where('status', \App\Enums\BorrowingStatus::Lost)->count();

    $periodeText = 'Semua Waktu';
    if (! empty($filters['filter'])) {
        $periodeText = match ($filters['filter']) {
            'hari_ini' => 'Hari Ini',
            'bulan_ini' => 'Bulan Ini',
            'tahun_ini' => 'Tahun Ini',
            default => ucfirst(str_replace('_', ' ', $filters['filter'])),
        };
    }
    if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
        $periodeText .= ' (' . \Carbon\Carbon::parse($filters['start_date'])->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($filters['end_date'])->format('d-m-Y') . ')';
    }

    $statusFilterText = ! empty($filters['status'])
        ? (\App\Enums\BorrowingStatus::tryFrom($filters['status'])?->label() ?? strtoupper($filters['status']))
        : 'Semua Status';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sirkulasi - {{ $namaPerpus }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 18mm 14mm 20mm 14mm;
        }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            color: #0A0A0A;
            margin: 0;
            padding: 0;
        }
        .mono {
            font-family: "Courier", monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table {
            border: 3px solid #0A0A0A;
            background-color: #FFF6E0;
            margin-bottom: 14px;
        }
        .kop-logo-cell {
            width: 72px;
            padding: 10px;
            border-right: 3px solid #0A0A0A;
            text-align: center;
            vertical-align: middle;
            background-color: #F9C32E;
        }
        .kop-monogram {
            font-family: "Courier", monospace;
            font-size: 20px;
            font-weight: bold;
            color: #0A0A0A;
        }
        .kop-info-cell {
            padding: 10px 14px;
            vertical-align: middle;
        }
        .kop-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
        }
        .kop-subtitle {
            font-size: 10px;
            color: #4f4633;
            margin: 0;
            line-height: 1.4;
        }
        .kop-meta-cell {
            width: 240px;
            padding: 10px 12px;
            border-left: 3px solid #0A0A0A;
            background-color: #FFFFFF;
            vertical-align: middle;
            font-size: 10px;
        }
        .report-heading {
            background-color: #0A0A0A;
            color: #FFFFFF;
            padding: 7px 12px;
            font-family: "Courier", monospace;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .summary-table {
            margin-bottom: 14px;
        }
        .summary-table td {
            border: 2px solid #0A0A0A;
            padding: 7px 10px;
            width: 20%;
            vertical-align: top;
        }
        .summary-label {
            font-family: "Courier", monospace;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
        }
        .data-table thead {
            display: table-header-group;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #F9C32E;
            border: 2px solid #0A0A0A;
            padding: 7px 6px;
            font-family: "Courier", monospace;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }
        .data-table td {
            border: 2px solid #0A0A0A;
            padding: 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .row-even {
            background-color: #FFF6E0;
        }
        .row-odd {
            background-color: #FFFFFF;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1.5px solid #0A0A0A;
            font-family: "Courier", monospace;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-dipinjam { background-color: #F9C32E; color: #0A0A0A; }
        .badge-dikembalikan { background-color: #C4F04D; color: #0A0A0A; }
        .badge-terlambat { background-color: #FF7A9C; color: #0A0A0A; }
        .badge-hilang { background-color: #0A0A0A; color: #FFFFFF; }
        .signature-table {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 240px;
            border: 2px solid #0A0A0A;
            background-color: #FFFFFF;
            padding: 10px 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Kop Perpustakaan dari SystemSetting --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-width: 52px; max-height: 52px;">
                @else
                    <div class="kop-monogram">{{ strtoupper(mb_substr($namaPerpus, 0, 2)) }}</div>
                @endif
            </td>
            <td class="kop-info-cell">
                <div class="kop-title">{{ $namaPerpus }}</div>
                <p class="kop-subtitle">
                    {{ $alamatPerpus ?: 'Sistem Informasi Perpustakaan' }}<br>
                    @if ($emailPerpus || $teleponPerpus)
                        @if ($emailPerpus) Email: {{ $emailPerpus }} @endif
                        @if ($emailPerpus && $teleponPerpus) | @endif
                        @if ($teleponPerpus) Telp: {{ $teleponPerpus }} @endif
                    @endif
                </p>
            </td>
            <td class="kop-meta-cell mono">
                <strong>PERIODE:</strong> {{ $periodeText }}<br>
                <strong>STATUS:</strong> {{ $statusFilterText }}<br>
                <strong>DICETAK:</strong> {{ now()->locale('id')->translatedFormat('d M Y H:i') }}
            </td>
        </tr>
    </table>

    <div class="report-heading">
        Laporan Sirkulasi Peminjaman Buku
    </div>

    {{-- Ringkasan Nyata Per Status (tanpa denda / klaim palsu) --}}
    <table class="summary-table">
        <tr>
            <td style="background-color: #F9C32E;">
                <span class="summary-label">Total Transaksi</span>
                <span class="summary-value">{{ $totalCount }}</span>
            </td>
            <td style="background-color: #FFFFFF;">
                <span class="summary-label">Dipinjam</span>
                <span class="summary-value">{{ $countBorrowed }}</span>
            </td>
            <td style="background-color: #C4F04D;">
                <span class="summary-label">Dikembalikan</span>
                <span class="summary-value">{{ $countReturned }}</span>
            </td>
            <td style="background-color: #FF7A9C;">
                <span class="summary-label">Terlambat</span>
                <span class="summary-value">{{ $countOverdue }}</span>
            </td>
            <td style="background-color: #EBE7E7;">
                <span class="summary-label">Hilang</span>
                <span class="summary-value">{{ $countLost }}</span>
            </td>
        </tr>
    </table>

    {{-- Tabel Data Sirkulasi --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Peminjam</th>
                <th width="28%">Judul Buku</th>
                <th width="12%" class="text-center">Tgl Pinjam</th>
                <th width="12%" class="text-center">Batas Kembali</th>
                <th width="12%" class="text-center">Tgl Kembali</th>
                <th width="11%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($borrowings as $index => $row)
                @php
                    $isLate = $row->isOverdue();
                    $badgeClass = match (true) {
                        $row->status === \App\Enums\BorrowingStatus::Lost => 'badge-hilang',
                        $row->status === \App\Enums\BorrowingStatus::Returned => 'badge-dikembalikan',
                        $row->status === \App\Enums\BorrowingStatus::Overdue || $isLate => 'badge-terlambat',
                        default => 'badge-dipinjam',
                    };
                    $statusText = $isLate ? 'Terlambat' : $row->status->label();
                @endphp
                <tr class="{{ $index % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td class="text-center mono">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $row->member->name ?? '-' }}</strong><br>
                        <span class="mono" style="font-size: 9px; color: #4f4633;">{{ $row->member->email ?? '' }}</span>
                    </td>
                    <td>{{ $row->book->title ?? '-' }}</td>
                    <td class="text-center mono">{{ $row->borrow_date?->format('d-m-Y') ?? '-' }}</td>
                    <td class="text-center mono">{{ $row->due_date?->format('d-m-Y') ?? '-' }}</td>
                    <td class="text-center mono">{{ $row->return_date?->format('d-m-Y') ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 18px; background-color: #FFF6E0;">
                        Tidak ada data transaksi sirkulasi pada filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan Petugas --}}
    <table class="signature-table">
        <tr>
            <td style="border: none;"></td>
            <td style="border: none; width: 260px;" align="right">
                <div class="signature-box">
                    <div class="mono" style="font-size: 9px; text-transform: uppercase;">Dicetak &amp; Disahkan Oleh</div>
                    <div style="height: 44px;"></div>
                    <div style="border-top: 2px solid #0A0A0A; padding-top: 5px;">
                        <strong>{{ $printerName }}</strong><br>
                        <span class="mono" style="font-size: 9px; text-transform: uppercase;">{{ $printerRole }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Nomor Halaman Nyata (DomPDF) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Courier", "bold");
            $size = 9;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $width = $fontMetrics->getTextWidth("Halaman 00 dari 00", $font, $size);
            $x = $pdf->get_width() - $width - 40;
            $y = $pdf->get_height() - 28;
            $pdf->page_text($x, $y, $text, $font, $size, [0.04, 0.04, 0.04]);
        }
    </script>
</body>
</html>
