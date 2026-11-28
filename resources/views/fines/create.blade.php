@extends('layouts.app')

@section('title', 'Bayar Denda - PerpusApp')
@section('page-title', 'Bayar Denda')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="card-title mb-1">Catat Pembayaran Denda</h4>
                    <p class="text-muted small mb-0">Transaksi peminjaman #{{ $borrowing->id }}</p>
                </div>
                <a href="{{ route('fines.index') }}" class="btn btn-light-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body pt-4">
                <div class="perpus-preview-box p-3 mb-4">
                    <div class="row g-3 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Peminjam</span>
                            <strong class="text-dark">{{ $borrowing->member?->name ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Judul Buku</span>
                            <strong class="text-dark">{{ $borrowing->book?->title ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Tanggal Kembali</span>
                            <strong class="text-dark">{{ $borrowing->return_date?->format('d M Y') ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Hari Terlambat</span>
                            <strong class="text-danger">{{ $borrowing->late_days }} hari</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Total Denda</span>
                            <strong class="text-danger">Rp{{ number_format($borrowing->fine_amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Sudah Dibayar</span>
                            <strong class="text-success">Rp{{ number_format($borrowing->paidFine(), 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <form action="{{ route('fines.store', $borrowing) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="amount" class="form-label fw-semibold">
                                Nominal Pembayaran <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       id="amount"
                                       name="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', $outstanding) }}"
                                       min="1"
                                       max="{{ $outstanding }}"
                                       step="1"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Sisa denda yang harus dibayar: Rp{{ number_format($outstanding, 0, ',', '.') }}</small>
                        </div>


                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Metode Pembayaran <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                @foreach($methods as $method)
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <input type="radio"
                                               class="btn-check"
                                               name="method"
                                               id="method-{{ $method->value }}"
                                               value="{{ $method->value }}"
                                               @checked(old('method', \App\Enums\PaymentMethod::Cash->value) === $method->value)
                                               required>
                                        <label class="btn btn-outline-secondary w-100 text-start d-flex align-items-center gap-2"
                                               for="method-{{ $method->value }}">
                                            <i class="bi {{ $method->icon() }}"></i>
                                            {{ $method->label() }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('method')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="reference" class="form-label fw-semibold">Nomor Referensi</label>
                            <input type="text"
                                   id="reference"
                                   name="reference"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   value="{{ old('reference') }}"
                                   maxlength="100"
                                   placeholder="Contoh: TRX-2026-00123">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="note" class="form-label fw-semibold">Catatan</label>
                            <textarea id="note"
                                      name="note"
                                      rows="3"
                                      maxlength="500"
                                      class="form-control @error('note') is-invalid @enderror"
                                      placeholder="Catatan tambahan mengenai pembayaran (opsional)">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('fines.index') }}" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

