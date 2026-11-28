@extends('layouts.app')

@section('title', 'Pembayaran Denda - PerpusApp')
@section('page-title', 'Pembayaran Denda')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-4">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body py-3">
                <h6 class="text-muted small mb-1">Total Transaksi Bayar</h6>
                <h4 class="font-extrabold mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body py-3">
                <h6 class="text-muted small mb-1">Total Denda Tertagih</h6>
                <h4 class="font-extrabold mb-0 text-success">
                    Rp{{ number_format($stats['sum'], 0, ',', '.') }}
                </h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card card-stat h-100 mb-0">
            <div class="card-body py-3">
                <h6 class="text-muted small mb-1">Denda Bulan Ini</h6>
                <h4 class="font-extrabold mb-0 text-primary">
                    Rp{{ number_format($stats['month'], 0, ',', '.') }}
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h5 class="card-title mb-1">Riwayat Pembayaran Denda</h5>
            <p class="text-muted small mb-0">Semua transaksi pembayaran denda yang tercatat di perpustakaan.</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="method" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Metode</option>
                @foreach(\App\Enums\PaymentMethod::cases() as $method)
                    <option value="{{ $method->value }}" @selected(request('method') === $method->value)>{{ $method->label() }}</option>
                @endforeach
            </select>
        </form>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="table1">
                <thead class="table-light">
                    <tr>
                        <th style="width: 55px;">No</th>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Nominal</th>
                        <th class="text-center">Metode</th>
                        <th>Dicatat Oleh</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $index => $payment)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $payment->member->name ?? 'Anggota Terhapus' }}</div>
                                <small class="text-muted">{{ $payment->member->email ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $payment->borrowing?->book?->title ?? 'Buku Terhapus' }}</div>
                                @if($payment->reference)
                                    <small class="text-muted">Ref: {{ $payment->reference }}</small>
                                @endif
                            </td>
                            <td class="fw-bold text-success">
                                Rp{{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light-primary text-primary fw-semibold">
                                    <i class="bi {{ $payment->method->icon() }} me-1"></i>{{ $payment->method->label() }}
                                </span>
                            </td>
                            <td class="small">{{ $payment->admin->name ?? 'Sistem' }}</td>
                            <td class="small">{{ $payment->created_at?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    @endforeach

                    @if($payments->isEmpty())
                        @include('partials.empty-row', [
                            'colspan' => 7,
                            'icon' => 'bi-cash-coin',
                            'title' => 'Belum Ada Pembayaran Denda',
                            'text' => 'Transaksi pembayaran denda akan tercatat di sini once petugas mencatat pembayaran.',
                        ])
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            {{ $payments->links() }}
            <span class="perpus-table-counter small">
                Menampilkan {{ $payments->firstItem() ?? 0 }}-{{ $payments->lastItem() ?? 0 }} dari {{ $payments->total() }} pembayaran
            </span>
        </div>
    </div>
</div>
@endsection

    </div>


