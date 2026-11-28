@extends('layouts.app')

@section('title', 'Sirkulasi & Laporan - PerpusApp')
@section('page-title', 'Data Sirkulasi & Laporan')

@section('content')
@php
    $totalTransaksi = $stats['total'];
    $aktifDipinjam = $stats['dipinjam'];
    $terlambatCount = $stats['terlambat'];
    $selesaiCount = $stats['selesai'];
@endphp

<div class="space-y-6">
    {{-- Header & Aksi Ekspor --}}
    <x-ui.card :window="true" title="borrowings.index" bodyClass="p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Daftar Transaksi Peminjaman</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Kelola peminjaman aktif, pengembalian, perpanjangan, serta ekspor laporan PDF dan CSV.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <x-ui.button variant="primary" icon="add" href="{{ route('borrowings.create') }}">
                    Pinjamkan Buku
                </x-ui.button>
                <x-ui.button variant="danger" icon="picture_as_pdf" href="{{ route('borrowings.report', request()->query()) }}" target="_blank">
                    PDF
                </x-ui.button>
                <x-ui.button variant="success" icon="table_view" href="{{ route('borrowings.report-csv', request()->query()) }}">
                    CSV
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

    {{-- 4 Kartu Statistik dari DB --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-ui.stat-card
            color="yellow"
            icon="sync_alt"
            badge="Total"
            label="Total Transaksi"
            :value="number_format($totalTransaksi, 0, ',', '.')"
        />
        <x-ui.stat-card
            color="sky"
            icon="hourglass_top"
            badge="Aktif"
            label="Sedang Dipinjam"
            :value="number_format($aktifDipinjam, 0, ',', '.')"
        />
        <x-ui.stat-card
            color="pink"
            icon="crisis_alert"
            badge="Tenggat"
            label="Terlambat"
            :value="number_format($terlambatCount, 0, ',', '.')"
        />
        <x-ui.stat-card
            color="mint"
            icon="check_circle"
            badge="Selesai"
            label="Dikembalikan"
            :value="number_format($selesaiCount, 0, ',', '.')"
        />
    </div>

    {{-- Filter Query String --}}
    <form action="{{ route('borrowings.index') }}" method="GET" class="bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-4 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <div class="lg:col-span-3">
                <x-ui.input
                    name="search"
                    id="tableSearch"
                    label="Pencarian"
                    :value="request('search')"
                    placeholder="Cari peminjam atau judul buku..."
                />
            </div>

            <div class="lg:col-span-2">
                <x-ui.input name="filter" id="filterPeriod" type="select" label="Periode Cepat">
                    <option value="">Semua Waktu</option>
                    <option value="hari_ini" {{ request('filter') === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="bulan_ini" {{ request('filter') === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun_ini" {{ request('filter') === 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                </x-ui.input>
            </div>

            <div class="lg:col-span-2">
                <x-ui.input name="status" id="filterStatus" type="select" label="Status">
                    <option value="">Semua Status</option>
                    @foreach (\App\Enums\BorrowingStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </x-ui.input>
            </div>

            <div class="lg:col-span-2">
                <x-ui.input name="start_date" id="startDate" type="date" label="Dari Tanggal" :value="request('start_date')" />
            </div>

            <div class="lg:col-span-2">
                <x-ui.input name="end_date" id="endDate" type="date" label="Sampai Tanggal" :value="request('end_date')" />
            </div>

            <div class="lg:col-span-1 flex items-center gap-2">
                <x-ui.button type="submit" variant="primary" size="md" class="w-full" title="Terapkan Filter">
                    Filter
                </x-ui.button>
            </div>
        </div>

        @if (request()->hasAny(['search', 'filter', 'status', 'start_date', 'end_date']) && (request('search') || request('filter') || request('status') || request('start_date') || request('end_date')))
            <div class="flex justify-end">
                <x-ui.button variant="secondary" size="sm" icon="restart_alt" href="{{ route('borrowings.index') }}">
                    Reset Filter
                </x-ui.button>
            </div>
        @endif
    </form>

    {{-- Tabel Transaksi --}}
    @if ($borrowings->isNotEmpty())
        <x-ui.table :headers="['No', 'Peminjam', 'Judul Buku', 'Petugas', 'Tgl Pinjam', 'Batas Kembali', 'Status', 'Aksi']">
            @foreach ($borrowings as $index => $row)
                @php
                    $dueDate = $row->due_date?->copy()->startOfDay() ?? now()->startOfDay();
                    $isLate = $row->isOverdue();
                    $lateDays = $isLate ? max(1, (int) $dueDate->diffInDays(now()->startOfDay())) : 0;
                    $badgeStatus = $isLate ? 'TERLAMBAT' : $row->status->value;
                @endphp
                <tr class="{{ $isLate ? '!bg-[#FF7A9C]/35' : '' }}">
                    <td class="font-mono text-xs w-14">{{ $borrowings->firstItem() + $index }}</td>
                    <td>
                        <div class="font-bold text-[#0A0A0A]">{{ $row->member->name ?? 'Anggota Terhapus' }}</div>
                        @if ($row->member)
                            <span class="font-mono text-[11px] text-[#4f4633]">{{ $row->member->email }}</span>
                        @endif
                    </td>
                    <td class="font-bold">{{ $row->book->title ?? 'Buku Dihapus' }}</td>
                    <td>
                        @if ($row->admin)
                            <div class="font-semibold text-[13px]">{{ $row->admin->name }}</div>
                            <span class="font-mono text-[10px] uppercase text-[#4f4633]">{{ $row->admin->role->label() }}</span>
                        @else
                            <span class="font-mono text-xs text-[#4f4633]">-</span>
                        @endif
                    </td>
                    <td class="font-mono text-xs">{{ $row->borrow_date?->format('d M Y') ?? '-' }}</td>
                    <td>
                        <div class="font-mono text-xs font-bold {{ $isLate ? 'text-[#93000a]' : 'text-[#0A0A0A]' }}">
                            {{ $dueDate->format('d M Y') }}
                        </div>
                        @if ($isLate)
                            <span class="inline-block mt-1 bg-[#FF7A9C] text-[#0A0A0A] border-[2px] border-[#0A0A0A] px-1.5 py-0.5 font-mono text-[10px] font-bold uppercase">
                                LEWAT {{ $lateDays }} HARI
                            </span>
                        @endif
                    </td>
                    <td>
                        <x-ui.badge :status="$badgeStatus" />
                    </td>
                    <td class="w-52">
                        @if ($row->status->isActive())
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if (! $isLate)
                                    <x-ui.button variant="secondary" size="sm" data-modal-open="extendModal{{ $row->id }}">
                                        Perpanjang
                                    </x-ui.button>
                                @endif
                                <x-ui.button variant="primary" size="sm" data-modal-open="returnModal{{ $row->id }}">
                                    Kembalikan
                                </x-ui.button>
                            </div>
                        @else
                            <x-ui.badge variant="success">Selesai</x-ui.badge>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div id="tableEmpty" class="hidden"></div>

        <x-ui.pagination :paginator="$borrowings" />
    @else
        <div id="tableEmpty">
            <x-ui.empty-state
                icon="sync_alt"
                title="Tidak Ada Data Transaksi"
                description="Belum ada transaksi peminjaman pada filter atau periode yang dipilih."
            >
                <x-ui.button variant="primary" size="sm" icon="add" href="{{ route('borrowings.create') }}">
                    Catat Peminjaman Baru
                </x-ui.button>
            </x-ui.empty-state>
        </div>
    @endif
</div>
@endsection

@push('modals')
    @foreach ($borrowings as $row)
        @if ($row->status->isActive())
            {{-- Modal Pengembalian (tanpa input/teks denda) --}}
            <x-ui.modal id="returnModal{{ $row->id }}" title="Proses Pengembalian Buku">
                <form id="returnForm{{ $row->id }}" action="{{ route('borrowings.return', $row) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="border-[2px] border-[#0A0A0A] bg-[#FFF6E0] p-3 space-y-1 font-sans text-[13px]">
                        <div>Peminjam: <strong>{{ $row->member->name ?? '-' }}</strong></div>
                        <div>Buku: <strong>{{ $row->book->title ?? '-' }}</strong></div>
                        <div>Batas Kembali: <strong class="{{ $row->isOverdue() ? 'text-[#93000a]' : '' }}">{{ $row->due_date?->format('d M Y') }}</strong></div>
                    </div>

                    <x-ui.input name="status" id="return_status_{{ $row->id }}" type="select" label="Status Pengembalian" :required="true">
                        <option value="{{ \App\Enums\BorrowingStatus::Returned->value }}" selected>Dikembalikan (Normal)</option>
                        <option value="{{ \App\Enums\BorrowingStatus::Lost->value }}">Buku Hilang</option>
                    </x-ui.input>
                </form>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="returnModal{{ $row->id }}">Batal</x-ui.button>
                    <x-ui.button type="submit" form="returnForm{{ $row->id }}" variant="primary" size="sm" icon="check">
                        Simpan Pengembalian
                    </x-ui.button>
                </x-slot:footer>
            </x-ui.modal>

            {{-- Modal Perpanjangan --}}
            @if (! $row->isOverdue())
                <x-ui.modal id="extendModal{{ $row->id }}" title="Perpanjang Masa Peminjaman">
                    <form id="extendForm{{ $row->id }}" action="{{ route('borrowings.extend', $row) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="border-[2px] border-[#0A0A0A] bg-[#FFF6E0] p-3 space-y-1 font-sans text-[13px]">
                            <div>Peminjam: <strong>{{ $row->member->name ?? '-' }}</strong></div>
                            <div>Buku: <strong>{{ $row->book->title ?? '-' }}</strong></div>
                            <div>Batas Kembali Saat Ini: <strong>{{ $row->due_date?->format('d M Y') }}</strong></div>
                        </div>

                        <x-ui.input
                            name="days"
                            id="extend_days_{{ $row->id }}"
                            type="number"
                            label="Tambah Durasi (Hari)"
                            value="7"
                            min="1"
                            max="30"
                            hint="Masukkan jumlah hari tambahan dari batas kembali saat ini."
                            :required="true"
                        />
                    </form>
                    <x-slot:footer>
                        <x-ui.button variant="secondary" size="sm" data-modal-close="extendModal{{ $row->id }}">Batal</x-ui.button>
                        <x-ui.button type="submit" form="extendForm{{ $row->id }}" variant="primary" size="sm" icon="update">
                            Perpanjang
                        </x-ui.button>
                    </x-slot:footer>
                </x-ui.modal>
            @endif
        @endif
    @endforeach
@endpush
