@extends('layouts.app')

@section('title', 'Proses Peminjaman - PerpusApp')
@section('page-title', 'Form Peminjaman Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
                <div>
                    <h4 class="card-title mb-1">Input Transaksi Peminjaman Baru</h4>
                    <p class="text-muted small mb-0">Pilih anggota peminjam dan judul buku yang tersedia di rak.</p>
                </div>
                <a href="{{ route('borrowings.index') }}" class="btn btn-light-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body pt-4">
                <form action="{{ route('borrowings.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <!-- Pilih Anggota -->
                        <div class="col-12">
                            <label for="member_id" class="form-label fw-semibold">
                                Anggota Peminjam <span class="text-danger">*</span>
                            </label>
                            <select id="member_id"
                                    name="member_id"
                                    class="form-select @error('member_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Anggota Peminjam --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} ({{ $member->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Pilih Buku -->
                        <div class="col-12">
                            <label for="book_id" class="form-label fw-semibold">
                                Buku yang Dipinjam <span class="text-danger">*</span>
                            </label>
                            <select id="book_id"
                                    name="book_id"
                                    class="form-select @error('book_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Koleksi Buku --</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}"
                                            data-author="{{ $book->author }}"
                                            data-isbn="{{ $book->isbn ?? '-' }}"
                                            data-stock="{{ $book->available_stock }}"
                                            data-fine="Rp{{ number_format($book->fine_per_day ?? 1000, 0, ',', '.') }}"
                                            {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                        {{ $book->title }} (Tersedia: {{ $book->available_stock }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>Hanya buku dengan stok tersedia yang ditampilkan.
                            </small>
                            @error('book_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Durasi Peminjaman -->
                        <div class="col-12">
                            <label for="duration_days" class="form-label fw-semibold">
                                Durasi Peminjaman (Hari) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   id="duration_days"
                                   name="duration_days"
                                   class="form-control @error('duration_days') is-invalid @enderror"
                                   value="{{ old('duration_days', $maxLoanDays) }}"
                                   min="1"
                                   max="{{ $maxLoanDays }}"
                                   required>
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>Maksimal {{ $maxLoanDays }} hari sesuai pengaturan sistem.
                            </small>
                            @error('duration_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Ringkasan Buku & Estimasi Tenggat Otomatis -->
                        <div class="col-12">
                            <div id="bookPreviewCard" class="p-3 perpus-preview-box d-none">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-light-primary text-primary fw-semibold">
                                        <i class="bi bi-journal-check me-1"></i>Ringkasan Buku Dipilih
                                    </span>
                                    <span class="badge bg-light-success text-success fw-semibold">
                                        Sisa Stok: <span id="previewStock">0</span>
                                    </span>
                                </div>
                                <div class="row g-2 small text-muted mt-1">
                                    <div class="col-sm-6">
                                        Penulis: <strong class="text-dark" id="previewAuthor">-</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        ISBN: <strong class="text-dark" id="previewIsbn">-</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        Tarif Denda: <strong class="text-danger" id="previewFine">-</strong> / hari
                                    </div>
                                    <div class="col-sm-6">
                                        Tgl Pinjam: <strong class="text-dark">{{ \Carbon\Carbon::now()->format('d M Y') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('borrowings.index') }}" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Proses Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookSelect = document.getElementById('book_id');
        const previewCard = document.getElementById('bookPreviewCard');

        function updateBookPreview() {
            const selected = bookSelect.options[bookSelect.selectedIndex];
            if (selected && selected.value) {
                document.getElementById('previewAuthor').textContent = selected.dataset.author;
                document.getElementById('previewIsbn').textContent = selected.dataset.isbn;
                document.getElementById('previewStock').textContent = selected.dataset.stock;
                document.getElementById('previewFine').textContent = selected.dataset.fine;
                previewCard.classList.remove('d-none');
            } else {
                previewCard.classList.add('d-none');
            }
        }

        if (bookSelect) {
            bookSelect.addEventListener('change', updateBookPreview);
            updateBookPreview();
        }
    });
</script>
@endpush