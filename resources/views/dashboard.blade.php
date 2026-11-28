@extends('layouts.app')

@section('title', 'Dashboard - PerpusApp')
@section('page-title', 'Dashboard Perpustakaan')

@section('content')
@php
    $currentUser = Auth::user();
    $booksList = $booksList ?? collect();
    $anggotaList = $anggotaList ?? collect();
    $dipinjamList = $dipinjamList ?? collect();

    $countBuku = $countBuku ?? $booksList->count();
    $countStokBuku = $countStokBuku ?? $booksList->sum('total_stock');
    $countAnggota = $countAnggota ?? $anggotaList->count();
    $countDipinjam = $countDipinjam ?? $dipinjamList->count();

    $recentActivities = $recentActivities ?? collect();
    $overdueCount = $overdueCount ?? 0;

    // 1. Tren peminjaman 6 bulan terakhir (batang HTML/CSS dari query DB)
    $startMonth = now()->subMonths(5)->startOfMonth();
    $monthlyRows = \App\Models\Borrowing::query()
        ->whereDate('borrow_date', '>=', $startMonth->toDateString())
        ->get(['borrow_date'])
        ->groupBy(fn (\App\Models\Borrowing $b) => $b->borrow_date?->format('Y-m'))
        ->map(fn ($g) => $g->count());

    $barPalette = ['#d1c2ff', '#F9C32E', '#4FA8FF', '#A9D9BC', '#C4F04D', '#FF7A9C'];
    $monthlyTrend = [];
    for ($i = 0; $i < 6; $i++) {
        $month = $startMonth->copy()->addMonths($i);
        $key = $month->format('Y-m');
        $monthlyTrend[] = [
            'label' => strtoupper($month->locale('id')->translatedFormat('M')),
            'fullLabel' => $month->locale('id')->translatedFormat('F Y'),
            'count' => (int) ($monthlyRows[$key] ?? 0),
            'color' => $barPalette[$i % count($barPalette)],
        ];
    }
    $maxMonthly = max(1, collect($monthlyTrend)->max('count'));
    $totalMonthly = collect($monthlyTrend)->sum('count');
    $peakMonth = collect($monthlyTrend)->sortByDesc('count')->first();

    // 2. Donut distribusi kategori (hitung stroke-dasharray di server)
    $categoryChart = $categoryChart ?? ['labels' => [], 'values' => [], 'colors' => []];
    $catTotal = array_sum($categoryChart['values']);
    $neoPalette = ['#7B4DFF', '#F9C32E', '#4FA8FF', '#C4F04D', '#FF7A9C', '#A9D9BC', '#FF8A5B', '#F3B6F5'];
    $circumference = 2 * pi() * 38;
    $runningOffset = 0.0;
    $donutSegments = [];

    foreach ($categoryChart['labels'] as $idx => $catLabel) {
        $val = (int) ($categoryChart['values'][$idx] ?? 0);
        if ($catTotal <= 0 || $val <= 0) {
            continue;
        }
        $pct = round(($val / $catTotal) * 100);
        $dash = ($val / $catTotal) * $circumference;
        $gap = max(0, $circumference - $dash);
        $donutSegments[] = [
            'label' => $catLabel,
            'count' => $val,
            'percent' => $pct,
            'color' => $neoPalette[$idx % count($neoPalette)],
            'dasharray' => number_format($dash, 2, '.', '') . ' ' . number_format($gap, 2, '.', ''),
            'dashoffset' => number_format(-$runningOffset, 2, '.', ''),
        ];
        $runningOffset += $dash;
    }

    $latestBooks = $booksList->sortByDesc('id')->take(5);
    $latestMembers = $anggotaList->take(5);
    $latestBorrowings = $dipinjamList->take(5);
@endphp

<div class="space-y-8">
    {{-- Banner Sambutan --}}
    <x-ui.card :window="true" title="session.user" bodyClass="p-6 bg-[#FFF6E0]">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-headline-lg text-[24px] md:text-[28px] font-extrabold text-[#0A0A0A] leading-tight m-0">
                        Halo, {{ $currentUser->name }}!
                    </h2>
                    <x-ui.badge variant="violet">{{ strtoupper($currentUser->role->label()) }}</x-ui.badge>
                </div>
                <p class="font-mono text-[12px] font-bold text-[#4f4633] flex items-center gap-2 m-0">
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                    <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button variant="primary" icon="sync_alt" href="{{ route('borrowings.index') }}">
                    Kelola Sirkulasi
                </x-ui.button>
                <x-ui.button variant="secondary" icon="menu_book" href="{{ route('books.index') }}">
                    Data Buku
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

    {{-- Strip Peringatan Buku Lewat Jatuh Tempo (hanya muncul jika ada) --}}
    @if ($overdueCount > 0)
        <div class="bg-[#FF7A9C] border-[3px] border-[#0A0A0A] neo-shadow-md p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 border-[3px] border-[#0A0A0A] bg-[#F9C32E] flex items-center justify-center shrink-0 neo-shadow-sm">
                    <span class="material-symbols-outlined text-[#0A0A0A] text-[24px]">crisis_alert</span>
                </div>
                <div>
                    <span class="font-headline-sm text-base font-extrabold text-[#0A0A0A] block">
                        PERHATIAN: {{ $overdueCount }} BUKU MELEWATI BATAS PENGEMBALIAN!
                    </span>
                    <span class="font-mono text-xs text-[#0A0A0A]/90">
                        Segera tindak lanjuti pengembalian pada halaman sirkulasi.
                    </span>
                </div>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow_forward" href="{{ route('borrowings.index') }}">
                Lihat Sirkulasi
            </x-ui.button>
        </div>
    @endif

    {{-- 4 Kartu Statistik Wajib --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-ui.stat-card
            color="yellow"
            icon="menu_book"
            badge="Judul"
            label="Total Koleksi"
            :value="number_format($countBuku, 0, ',', '.')"
            footerLeft="Katalog Buku"
            footerRight="Lihat Rincian"
            role="button"
            tabindex="0"
            data-modal-open="koleksiBukuModal"
            class="cursor-pointer"
        />

        <x-ui.stat-card
            color="mint"
            icon="inventory_2"
            badge="Eksemplar"
            label="Jumlah Stok Buku"
            :value="number_format($countStokBuku, 0, ',', '.')"
            footerLeft="Total Stok"
            footerRight="Lihat Rincian"
            role="button"
            tabindex="0"
            data-modal-open="koleksiBukuModal"
            class="cursor-pointer"
        />

        <x-ui.stat-card
            color="sky"
            icon="badge"
            badge="Anggota"
            label="Jumlah Member"
            :value="number_format($countAnggota, 0, ',', '.')"
            footerLeft="Data Anggota"
            footerRight="Lihat Rincian"
            role="button"
            tabindex="0"
            data-modal-open="anggotaModal"
            class="cursor-pointer"
        />

        <x-ui.stat-card
            color="pink"
            icon="swap_horiz"
            badge="Aktif"
            label="Sedang Dipinjam"
            :value="number_format($countDipinjam, 0, ',', '.')"
            footerLeft="Sirkulasi Aktif"
            footerRight="Lihat Rincian"
            role="button"
            tabindex="0"
            data-modal-open="bukuDipinjamModal"
            class="cursor-pointer"
        />
    </div>

    {{-- Grafik Tren 6 Bulan & Donut Distribusi Kategori --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Kiri: Tren Peminjaman 6 Bulan Terakhir --}}
        <div class="lg:col-span-7 bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b-[3px] border-[#0A0A0A] mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 border-[2px] border-[#0A0A0A] bg-[#FFF6E0] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px] text-[#0A0A0A]">bar_chart</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-base uppercase text-[#0A0A0A] m-0">Tren Peminjaman Buku</h2>
                            <span class="font-mono text-[11px] text-[#4f4633]">
                                {{ $monthlyTrend[0]['fullLabel'] }} &ndash; {{ $monthlyTrend[5]['fullLabel'] }}
                            </span>
                        </div>
                    </div>
                    <span class="bg-[#C4F04D] border-[2px] border-[#0A0A0A] px-2.5 py-1 font-mono text-[11px] font-bold">
                        TOTAL: {{ number_format($totalMonthly, 0, ',', '.') }} PINJAMAN
                    </span>
                </div>

                <div class="w-full h-64 relative flex items-end justify-between pt-8 pb-4 px-2">
                    <div class="absolute inset-x-2 top-8 bottom-4 flex flex-col justify-between pointer-events-none opacity-25">
                        <div class="w-full border-b-[2px] border-dashed border-[#0A0A0A]"></div>
                        <div class="w-full border-b-[2px] border-dashed border-[#0A0A0A]"></div>
                        <div class="w-full border-b-[2px] border-dashed border-[#0A0A0A]"></div>
                        <div class="w-full border-b-[2px] border-[#0A0A0A]"></div>
                    </div>

                    @foreach ($monthlyTrend as $bar)
                        @php
                            $heightPct = $bar['count'] > 0 ? max(8, (int) round(($bar['count'] / $maxMonthly) * 100)) : 4;
                        @endphp
                        <div class="relative flex flex-col items-center flex-1 z-10 h-full justify-end group">
                            <span class="font-mono text-[11px] font-bold mb-1 text-[#0A0A0A]">{{ $bar['count'] }}</span>
                            <div
                                class="w-8 md:w-11 border-[3px] border-[#0A0A0A] neo-shadow-sm transition-all"
                                style="height: {{ $heightPct }}%; background-color: {{ $bar['color'] }};"
                                title="{{ $bar['fullLabel'] }}: {{ $bar['count'] }} pinjaman"
                            ></div>
                            <span class="font-mono text-[11px] font-bold mt-2 uppercase text-[#0A0A0A]">{{ $bar['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-3 border-t-[3px] border-[#0A0A0A] flex items-center justify-between text-xs font-mono">
                <span class="text-[#0A0A0A]">
                    @if ($totalMonthly > 0 && $peakMonth)
                        Peminjaman tertinggi: {{ $peakMonth['fullLabel'] }} ({{ $peakMonth['count'] }} transaksi).
                    @else
                        Belum ada transaksi peminjaman dalam 6 bulan terakhir.
                    @endif
                </span>
            </div>
        </div>

        {{-- Kanan: Donut Distribusi Kategori --}}
        <div class="lg:col-span-5 bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-3 pb-4 border-b-[3px] border-[#0A0A0A] mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 border-[2px] border-[#0A0A0A] bg-[#FFF6E0] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px] text-[#0A0A0A]">pie_chart</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-base uppercase text-[#0A0A0A] m-0">Distribusi Kategori</h2>
                            <span class="font-mono text-[11px] text-[#4f4633]">Pinjaman Aktif Saat Ini</span>
                        </div>
                    </div>
                    <span class="bg-[#FFF6E0] border-[2px] border-[#0A0A0A] px-2 py-0.5 font-mono text-[10px] font-bold">
                        {{ count($donutSegments) }} KATEGORI
                    </span>
                </div>

                @if (count($donutSegments) > 0)
                    <div class="flex flex-col sm:flex-row items-center justify-around gap-6 py-2">
                        <div class="relative w-44 h-44 shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100" aria-label="Grafik distribusi kategori">
                                <circle cx="50" cy="50" fill="none" r="38" stroke="#0A0A0A" stroke-width="20"></circle>
                                @foreach ($donutSegments as $seg)
                                    <circle
                                        cx="50"
                                        cy="50"
                                        fill="none"
                                        r="38"
                                        stroke="{{ $seg['color'] }}"
                                        stroke-dasharray="{{ $seg['dasharray'] }}"
                                        stroke-dashoffset="{{ $seg['dashoffset'] }}"
                                        stroke-width="16"
                                    ></circle>
                                @endforeach
                                <circle cx="50" cy="50" fill="#FFFFFF" r="29" stroke="#0A0A0A" stroke-width="3"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="font-headline-sm text-base text-[#0A0A0A] font-extrabold leading-none">{{ $catTotal }}</span>
                                <span class="font-mono text-[9px] uppercase text-[#4f4633] font-bold mt-1">DIPINJAM</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 w-full">
                            @foreach (array_slice($donutSegments, 0, 5) as $seg)
                                <div class="flex items-center justify-between border-[2px] border-[#0A0A0A] p-2 bg-[#FFF6E0]">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-3.5 h-3.5 border-[2px] border-[#0A0A0A] shrink-0" style="background-color: {{ $seg['color'] }};"></div>
                                        <span class="font-headline-sm text-xs font-bold text-[#0A0A0A] truncate">{{ $seg['label'] }}</span>
                                    </div>
                                    <span class="font-mono text-xs font-bold text-[#0A0A0A] shrink-0 ml-2">{{ $seg['percent'] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-ui.empty-state
                        icon="pie_chart"
                        title="Belum Ada Pinjaman Aktif"
                        description="Distribusi kategori akan tampil setelah ada buku yang sedang dipinjam."
                    />
                @endif
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-headline-md text-[20px] font-extrabold uppercase text-[#0A0A0A] m-0">Aktivitas Terbaru</h2>
            <x-ui.button variant="secondary" size="sm" href="{{ route('borrowings.index') }}">Semua Sirkulasi</x-ui.button>
        </div>

        @if ($recentActivities->isNotEmpty())
            <x-ui.table :headers="['Anggota', 'Buku', 'Tanggal Pinjam', 'Status']">
                @foreach ($recentActivities as $activity)
                    <tr>
                        <td class="font-bold">{{ $activity->member->name ?? 'Anggota Terhapus' }}</td>
                        <td>{{ $activity->book->title ?? 'Buku Dihapus' }}</td>
                        <td class="font-mono text-xs">{{ $activity->borrow_date?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            @if ($activity->isOverdue())
                                <x-ui.badge status="TERLAMBAT" />
                            @else
                                <x-ui.badge :status="$activity->status->value" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @else
            <x-ui.empty-state
                icon="history"
                title="Belum Ada Aktivitas"
                description="Transaksi peminjaman dan pengembalian terbaru akan muncul di sini."
            />
        @endif
    </div>
</div>
@endsection

@push('modals')
    {{-- Modal 1: 5 Buku Terbaru --}}
    <x-ui.modal id="koleksiBukuModal" title="Koleksi Buku Terbaru" size="lg">
        @if ($latestBooks->isNotEmpty())
            <x-ui.table :headers="['Judul Buku', 'Penulis', 'Stok Tersedia']">
                @foreach ($latestBooks as $book)
                    <tr>
                        <td class="font-bold">{{ $book->title }}</td>
                        <td>{{ $book->author }}</td>
                        <td class="font-mono text-xs">{{ $book->available_stock }} / {{ $book->total_stock }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @else
            <x-ui.empty-state icon="menu_book" title="Belum Ada Buku" description="Belum ada koleksi buku terdaftar." />
        @endif

        <x-slot:footer>
            <x-ui.button variant="secondary" size="sm" data-modal-close="koleksiBukuModal">Tutup</x-ui.button>
            <x-ui.button variant="primary" size="sm" href="{{ route('books.index') }}">Lihat Semua Buku</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Modal 2: 5 Anggota Terbaru --}}
    <x-ui.modal id="anggotaModal" title="Anggota Terbaru" size="lg">
        @if ($latestMembers->isNotEmpty())
            <x-ui.table :headers="['Nama Anggota', 'Email', 'No. HP']">
                @foreach ($latestMembers as $member)
                    <tr>
                        <td class="font-bold">{{ $member->name }}</td>
                        <td class="font-mono text-xs">{{ $member->email }}</td>
                        <td class="font-mono text-xs">{{ $member->phone ?: '-' }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @else
            <x-ui.empty-state icon="badge" title="Belum Ada Anggota" description="Belum ada anggota terdaftar." />
        @endif

        <x-slot:footer>
            <x-ui.button variant="secondary" size="sm" data-modal-close="anggotaModal">Tutup</x-ui.button>
            <x-ui.button variant="primary" size="sm" href="{{ route('members.index') }}">Lihat Semua Anggota</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Modal 3: 5 Peminjaman Aktif Terbaru --}}
    <x-ui.modal id="bukuDipinjamModal" title="Sedang Dipinjam (Terbaru)" size="lg">
        @if ($latestBorrowings->isNotEmpty())
            <x-ui.table :headers="['Peminjam', 'Buku', 'Jatuh Tempo']">
                @foreach ($latestBorrowings as $pinjam)
                    <tr>
                        <td class="font-bold">{{ $pinjam->member->name ?? 'Anggota Terhapus' }}</td>
                        <td>{{ $pinjam->book->title ?? 'Buku Dihapus' }}</td>
                        <td class="font-mono text-xs">{{ $pinjam->due_date?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @else
            <x-ui.empty-state icon="swap_horiz" title="Tidak Ada Pinjaman Aktif" description="Semua buku sedang tersedia di perpustakaan." />
        @endif

        <x-slot:footer>
            <x-ui.button variant="secondary" size="sm" data-modal-close="bukuDipinjamModal">Tutup</x-ui.button>
            <x-ui.button variant="primary" size="sm" href="{{ route('borrowings.index') }}">Lihat Semua Sirkulasi</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
@endpush
