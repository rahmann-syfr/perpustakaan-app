@extends('layouts.app')

@section('title', 'Proses Peminjaman - PerpusApp')
@section('page-title', 'Form Peminjaman Buku')

@section('content')
@php
    $selectedMemberId = old('member_id', request('member_id'));
    $selectedBookId = old('book_id', request('book_id'));
    $defaultDuration = (int) old('duration_days', $maxLoanDays);
    $quickDurations = collect([3, 7, 14])->filter(fn (int $d) => $d <= $maxLoanDays);
    if ($quickDurations->isEmpty()) {
        $quickDurations = collect([$maxLoanDays]);
    }
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    @if ($errors->any())
        <x-ui.alert type="error" title="Gagal Memproses Peminjaman">
            {{ $errors->first() }}
        </x-ui.alert>
    @endif

    <form action="{{ route('borrowings.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf

        {{-- Kolom Kiri: Input Form --}}
        <div class="lg:col-span-7 space-y-6">
            <x-ui.card :window="true" title="borrowings.create" bodyClass="p-6 space-y-6">
                <div class="flex items-center justify-between gap-4 pb-4 border-b-[3px] border-[#0A0A0A]">
                    <div>
                        <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Input Peminjaman Baru</h2>
                        <p class="font-sans text-[13px] text-[#4f4633] mt-1 mb-0">
                            Pilih anggota peminjam, buku yang tersedia, serta durasi peminjaman.
                        </p>
                    </div>
                    <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('borrowings.index') }}">
                        Kembali
                    </x-ui.button>
                </div>

                {{-- Pilih Anggota dengan Pencarian Cepat --}}
                <div class="space-y-2">
                    <label for="member_id" class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A] block">
                        Anggota Peminjam <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input
                        type="text"
                        id="memberFilterInput"
                        placeholder="Ketik nama atau email untuk menyaring anggota..."
                        class="w-full bg-[#FFF6E0] text-[#0A0A0A] font-sans text-[13px] px-3 py-2 border-[2px] border-[#0A0A0A] focus:outline-none focus:border-[#7B4DFF]"
                    />
                    <select
                        id="member_id"
                        name="member_id"
                        required
                        class="w-full bg-white text-[#0A0A0A] font-sans text-[15px] font-medium px-4 py-3 border-[3px] {{ $errors->has('member_id') ? 'border-[#FF7A9C]' : 'border-[#0A0A0A]' }} neo-shadow-md focus:outline-none focus:border-[#7B4DFF]"
                    >
                        <option value="">-- Pilih Anggota Peminjam --</option>
                        @foreach ($members as $member)
                            <option
                                value="{{ $member->id }}"
                                data-search="{{ mb_strtolower($member->name . ' ' . $member->email) }}"
                                data-name="{{ $member->name }}"
                                data-email="{{ $member->email }}"
                                {{ (string) $selectedMemberId === (string) $member->id ? 'selected' : '' }}
                            >
                                {{ $member->name }} ({{ $member->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="font-mono text-[11px] font-bold text-[#93000a] bg-[#ffdad6] border-[2px] border-[#0A0A0A] px-2.5 py-1 m-0">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilih Buku dengan Pencarian Cepat --}}
                <div class="space-y-2">
                    <label for="book_id" class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A] block">
                        Buku yang Dipinjam <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input
                        type="text"
                        id="bookFilterInput"
                        placeholder="Ketik judul, penulis, atau ISBN untuk menyaring buku..."
                        class="w-full bg-[#FFF6E0] text-[#0A0A0A] font-sans text-[13px] px-3 py-2 border-[2px] border-[#0A0A0A] focus:outline-none focus:border-[#7B4DFF]"
                    />
                    <select
                        id="book_id"
                        name="book_id"
                        required
                        class="w-full bg-white text-[#0A0A0A] font-sans text-[15px] font-medium px-4 py-3 border-[3px] {{ $errors->has('book_id') ? 'border-[#FF7A9C]' : 'border-[#0A0A0A]' }} neo-shadow-md focus:outline-none focus:border-[#7B4DFF]"
                    >
                        <option value="">-- Pilih Koleksi Buku --</option>
                        @foreach ($books as $book)
                            <option
                                value="{{ $book->id }}"
                                data-search="{{ mb_strtolower($book->title . ' ' . $book->author . ' ' . ($book->isbn ?? '')) }}"
                                data-title="{{ $book->title }}"
                                data-author="{{ $book->author }}"
                                data-isbn="{{ $book->isbn ?? '-' }}"
                                data-stock="{{ $book->available_stock }}"
                                {{ (string) $selectedBookId === (string) $book->id ? 'selected' : '' }}
                            >
                                {{ $book->title }} (Tersedia: {{ $book->available_stock }})
                            </option>
                        @endforeach
                    </select>
                    <p class="font-sans text-[12px] text-[#4f4633] m-0">
                        Hanya buku dengan stok tersedia (&gt; 0) yang dapat dipilih.
                    </p>
                    @error('book_id')
                        <p class="font-mono text-[11px] font-bold text-[#93000a] bg-[#ffdad6] border-[2px] border-[#0A0A0A] px-2.5 py-1 m-0">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Durasi Peminjaman + Chip Cepat --}}
                <div class="space-y-3">
                    <x-ui.input
                        name="duration_days"
                        id="duration_days"
                        type="number"
                        label="Durasi Peminjaman (Hari)"
                        :value="$defaultDuration"
                        min="1"
                        :max="$maxLoanDays"
                        :hint="'Maksimal ' . $maxLoanDays . ' hari sesuai pengaturan sistem.'"
                        :required="true"
                    />

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">Pilih Cepat:</span>
                        @foreach ($quickDurations as $days)
                            <button
                                type="button"
                                data-duration-chip="{{ $days }}"
                                class="px-3 py-1 bg-[#FFF6E0] hover:bg-[#F9C32E] text-[#0A0A0A] border-[2px] border-[#0A0A0A] neo-shadow-sm font-mono text-[11px] font-bold uppercase cursor-pointer transition-colors"
                            >
                                {{ $days }} Hari
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t-[3px] border-[#0A0A0A]">
                    <x-ui.button variant="secondary" href="{{ route('borrowings.index') }}">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check_circle">Proses Peminjaman</x-ui.button>
                </div>
            </x-ui.card>
        </div>

        {{-- Kolom Kanan: Ringkasan Peminjaman & Batas Kembali Otomatis --}}
        <div class="lg:col-span-5 space-y-6">
            <x-ui.card :window="true" title="Ringkasan Transaksi" bodyClass="p-6 space-y-4">
                <div class="border-[3px] border-[#0A0A0A] bg-[#FFF6E0] p-4 space-y-2">
                    <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633] block">Peminjam</span>
                    <div id="summaryMemberName" class="font-headline-sm text-[16px] font-bold text-[#0A0A0A]">Belum dipilih</div>
                    <div id="summaryMemberEmail" class="font-mono text-[11px] text-[#4f4633]">-</div>
                </div>

                <div class="border-[3px] border-[#0A0A0A] bg-white p-4 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633]">Buku</span>
                        <span id="summaryBookStock" class="bg-[#C4F04D] border-[2px] border-[#0A0A0A] px-2 py-0.5 font-mono text-[10px] font-bold uppercase">
                            Stok: -
                        </span>
                    </div>
                    <div id="summaryBookTitle" class="font-headline-sm text-[16px] font-bold text-[#0A0A0A]">Belum dipilih</div>
                    <div class="font-sans text-[12px] text-[#4f4633]">
                        Penulis: <strong id="summaryBookAuthor">-</strong> &bull; ISBN: <code id="summaryBookIsbn" class="font-mono">-</code>
                    </div>
                </div>

                <div class="border-[3px] border-[#0A0A0A] bg-[#F9C32E] p-4 space-y-2">
                    <div class="flex items-center justify-between text-xs font-mono font-bold">
                        <span>TANGGAL PINJAM</span>
                        <span>{{ now()->locale('id')->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-mono font-bold pt-2 border-t-[2px] border-[#0A0A0A]">
                        <span>BATAS KEMBALI</span>
                        <span id="summaryDueDate">-</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const memberFilter = document.getElementById('memberFilterInput');
        const memberSelect = document.getElementById('member_id');
        const bookFilter = document.getElementById('bookFilterInput');
        const bookSelect = document.getElementById('book_id');
        const durationInput = document.getElementById('duration_days');

        function filterSelect(input, select) {
            if (!input || !select) return;
            input.addEventListener('input', function () {
                const q = input.value.trim().toLowerCase();
                Array.from(select.options).forEach(function (opt, idx) {
                    if (idx === 0) return;
                    const text = opt.getAttribute('data-search') || opt.textContent.toLowerCase();
                    opt.hidden = q !== '' && !text.includes(q);
                });
            });
        }

        function updateSummary() {
            if (memberSelect) {
                const opt = memberSelect.options[memberSelect.selectedIndex];
                document.getElementById('summaryMemberName').textContent = (opt && opt.value) ? opt.dataset.name : 'Belum dipilih';
                document.getElementById('summaryMemberEmail').textContent = (opt && opt.value) ? opt.dataset.email : '-';
            }

            if (bookSelect) {
                const opt = bookSelect.options[bookSelect.selectedIndex];
                if (opt && opt.value) {
                    document.getElementById('summaryBookTitle').textContent = opt.dataset.title;
                    document.getElementById('summaryBookAuthor').textContent = opt.dataset.author;
                    document.getElementById('summaryBookIsbn').textContent = opt.dataset.isbn;
                    document.getElementById('summaryBookStock').textContent = 'Stok: ' + opt.dataset.stock;
                } else {
                    document.getElementById('summaryBookTitle').textContent = 'Belum dipilih';
                    document.getElementById('summaryBookAuthor').textContent = '-';
                    document.getElementById('summaryBookIsbn').textContent = '-';
                    document.getElementById('summaryBookStock').textContent = 'Stok: -';
                }
            }

            if (durationInput) {
                const days = Math.max(1, parseInt(durationInput.value, 10) || 1);
                const due = new Date();
                due.setDate(due.getDate() + days);
                document.getElementById('summaryDueDate').textContent = due.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                });
            }
        }

        filterSelect(memberFilter, memberSelect);
        filterSelect(bookFilter, bookSelect);

        document.querySelectorAll('[data-duration-chip]').forEach(function (chip) {
            chip.addEventListener('click', function () {
                if (durationInput) {
                    durationInput.value = chip.getAttribute('data-duration-chip');
                    updateSummary();
                }
            });
        });

        memberSelect?.addEventListener('change', updateSummary);
        bookSelect?.addEventListener('change', updateSummary);
        durationInput?.addEventListener('input', updateSummary);

        updateSummary();
    });
</script>
@endpush
