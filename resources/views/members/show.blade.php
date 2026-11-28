@extends('layouts.app')

@section('title', 'Detail Anggota - PerpusApp')
@section('page-title', 'Detail Informasi Anggota')

@section('content')
@php
    $initial = strtoupper(mb_substr($member->name, 0, 1));
    $maxBooks = \App\Models\SystemSetting::getInt('maksimal_buku_anggota', 3);
    $activeCount = $borrowings->filter(fn ($b) => $b->status->isActive())->count();
    $overdueCount = $borrowings->filter(fn ($b) => $b->isOverdue())->count();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Kolom Kiri: Profil Anggota --}}
    <div class="lg:col-span-4 space-y-6">
        <x-ui.card :window="true" title="members.profile" bodyClass="p-6">
            <div class="flex flex-col items-center text-center pb-5 mb-5 border-b-[3px] border-[#0A0A0A]">
                <div class="w-20 h-20 rounded-full border-[3px] border-[#0A0A0A] bg-[#F9C32E] neo-shadow-md flex items-center justify-center font-display-md text-[32px] font-extrabold text-[#0A0A0A] mb-3">
                    {{ $initial }}
                </div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">{{ $member->name }}</h2>
                <div class="mt-2">
                    <x-ui.badge variant="mint">ANGGOTA AKTIF</x-ui.badge>
                </div>
            </div>

            <div class="border-[3px] border-[#0A0A0A] bg-white mb-5">
                <div class="flex items-center justify-between px-3.5 py-2.5 border-b-[2px] border-[#0A0A0A] bg-[#FFF6E0] gap-2">
                    <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633]">Email</span>
                    <span class="font-mono text-[12px] font-bold text-[#0A0A0A] truncate">{{ $member->email }}</span>
                </div>
                <div class="flex items-center justify-between px-3.5 py-2.5 border-b-[2px] border-[#0A0A0A] gap-2">
                    <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633]">No HP</span>
                    <span class="font-mono text-[12px] font-bold text-[#0A0A0A]">{{ $member->phone ?: '-' }}</span>
                </div>
                <div class="flex items-start justify-between px-3.5 py-2.5 border-b-[2px] border-[#0A0A0A] bg-[#FFF6E0] gap-3">
                    <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633] shrink-0">Alamat</span>
                    <span class="font-sans text-[13px] font-medium text-[#0A0A0A] text-right">{{ $member->address ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between px-3.5 py-2.5 border-b-[2px] border-[#0A0A0A] gap-2">
                    <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633]">Terdaftar</span>
                    <span class="font-mono text-[12px] font-bold text-[#0A0A0A]">{{ $member->created_at?->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between px-3.5 py-2.5 gap-2">
                    <span class="font-mono text-[10px] font-bold uppercase text-[#4f4633]">Kuota Pinjam</span>
                    <span class="font-mono text-[12px] font-bold text-[#0A0A0A]">{{ $activeCount }} / {{ $maxBooks }} buku</span>
                </div>
            </div>

            <div class="flex flex-col gap-2.5">
                <x-ui.button variant="primary" icon="add_circle" href="{{ route('borrowings.create', ['member_id' => $member->id]) }}" class="w-full">
                    Pinjamkan Buku
                </x-ui.button>
                <x-ui.button variant="secondary" icon="edit" href="{{ route('members.edit', $member) }}" class="w-full">
                    Edit Data Anggota
                </x-ui.button>
                <x-ui.button variant="secondary" icon="arrow_back" href="{{ route('members.index') }}" class="w-full">
                    Kembali
                </x-ui.button>
            </div>
        </x-ui.card>
    </div>

    {{-- Kolom Kanan: 3 Mini-Stat & Riwayat Sirkulasi --}}
    <div class="lg:col-span-8 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-[#F9C32E] border-[3px] border-[#0A0A0A] neo-shadow-md p-4">
                <span class="font-mono text-[10px] font-bold uppercase text-[#0A0A0A] block">Total Peminjaman</span>
                <span class="font-display-md text-[32px] font-extrabold text-[#0A0A0A] leading-tight">{{ $borrowings->count() }}</span>
            </div>
            <div class="bg-[#4FA8FF] border-[3px] border-[#0A0A0A] neo-shadow-md p-4">
                <span class="font-mono text-[10px] font-bold uppercase text-[#0A0A0A] block">Sedang Dipinjam</span>
                <span class="font-display-md text-[32px] font-extrabold text-[#0A0A0A] leading-tight">{{ $activeCount }}</span>
            </div>
            <div class="bg-[#FF7A9C] border-[3px] border-[#0A0A0A] neo-shadow-md p-4">
                <span class="font-mono text-[10px] font-bold uppercase text-[#0A0A0A] block">Keterlambatan</span>
                <span class="font-display-md text-[32px] font-extrabold text-[#0A0A0A] leading-tight">{{ $overdueCount }}</span>
            </div>
        </div>

        <x-ui.card title="Riwayat Sirkulasi" bodyClass="p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="font-sans text-[13px] text-[#4f4633] m-0">
                    Daftar seluruh transaksi peminjaman buku oleh anggota ini.
                </p>

                @if ($borrowings->isNotEmpty())
                    <select
                        id="memberHistoryFilter"
                        aria-label="Filter riwayat sirkulasi"
                        class="bg-white text-[#0A0A0A] font-mono text-[12px] font-bold uppercase px-3 py-2 border-[3px] border-[#0A0A0A] neo-shadow-sm focus:outline-none focus:border-[#7B4DFF]"
                    >
                        <option value="">Semua Status</option>
                        <option value="DIPINJAM">Dipinjam</option>
                        <option value="TERLAMBAT">Terlambat</option>
                        <option value="DIKEMBALIKAN">Dikembalikan</option>
                        <option value="HILANG">Hilang</option>
                    </select>
                @endif
            </div>

            @if ($borrowings->isNotEmpty())
                <x-ui.table :headers="['Judul Buku', 'Tgl Pinjam', 'Tenggat', 'Status']" id="memberHistoryTable">
                    @foreach ($borrowings as $item)
                        @php
                            $statusKey = $item->isOverdue() ? 'TERLAMBAT' : match ($item->status) {
                                \App\Enums\BorrowingStatus::Borrowed => 'DIPINJAM',
                                \App\Enums\BorrowingStatus::Returned => 'DIKEMBALIKAN',
                                \App\Enums\BorrowingStatus::Overdue => 'TERLAMBAT',
                                \App\Enums\BorrowingStatus::Lost => 'HILANG',
                            };
                        @endphp
                        <tr data-history-row data-status="{{ $statusKey }}">
                            <td class="font-bold">{{ $item->book->title ?? 'Buku Dihapus' }}</td>
                            <td class="font-mono text-xs">{{ \Carbon\Carbon::parse($item->borrow_date)->format('d M Y') }}</td>
                            <td class="font-mono text-xs">{{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}</td>
                            <td><x-ui.badge :status="$statusKey" /></td>
                        </tr>
                    @endforeach
                </x-ui.table>

                <div id="memberHistoryEmpty" class="hidden">
                    <x-ui.empty-state icon="filter_alt_off" title="Tidak Ada Transaksi" description="Tidak ada riwayat sirkulasi dengan status tersebut." />
                </div>
            @else
                <x-ui.empty-state
                    icon="menu_book"
                    title="Belum Pernah Meminjam"
                    description="Anggota ini belum memiliki riwayat peminjaman buku."
                />
            @endif
        </x-ui.card>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const filter = document.getElementById('memberHistoryFilter');
        const rows = document.querySelectorAll('[data-history-row]');
        const tableWrap = document.getElementById('memberHistoryTable');
        const emptyBox = document.getElementById('memberHistoryEmpty');

        if (!filter || !rows.length) return;

        filter.addEventListener('change', function () {
            const val = filter.value;
            let visible = 0;

            rows.forEach(function (row) {
                const match = !val || row.getAttribute('data-status') === val;
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            if (tableWrap && emptyBox) {
                tableWrap.classList.toggle('hidden', visible === 0);
                emptyBox.classList.toggle('hidden', visible > 0);
            }
        });
    })();
</script>
@endpush
