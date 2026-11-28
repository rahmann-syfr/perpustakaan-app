@extends('layouts.app')

@section('title', 'UI Kit Neo-Brutalist - PerpusApp')
@section('page-title', 'UI Kit Komponen')

@section('content')
    <div class="space-y-8">
        <x-ui.card :window="true" title="ui-kit.blade.php" rightLabel="LOCAL PREVIEW" sticker="NEO-BRUTALIST">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-headline-lg text-[24px] font-extrabold text-[#0A0A0A] m-0">Sistem Komponen PerpusApp</h2>
                    <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                        Halaman pemeriksaan komponen Blade <code class="font-mono">x-ui.*</code> sesuai token <code class="font-mono">DESIGN.md</code>.
                    </p>
                </div>
                <x-ui.button variant="primary" icon="add" data-modal-open="uiKitModal">Buka Contoh Modal</x-ui.button>
            </div>
        </x-ui.card>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">1. Tombol (5 Varian)</h3>
            <x-ui.card>
                <div class="flex flex-wrap items-center gap-4">
                    <x-ui.button variant="primary" icon="save">Primary</x-ui.button>
                    <x-ui.button variant="secondary" icon="arrow_back">Secondary</x-ui.button>
                    <x-ui.button variant="success" icon="check">Success</x-ui.button>
                    <x-ui.button variant="danger" icon="delete">Danger</x-ui.button>
                    <x-ui.button variant="info" icon="info">Info</x-ui.button>
                </div>
            </x-ui.card>
        </section>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">2. Badge Status</h3>
            <x-ui.card>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.badge status="DIPINJAM" :dot="true" />
                    <x-ui.badge status="DIKEMBALIKAN" :dot="true" />
                    <x-ui.badge status="TERLAMBAT" :dot="true" />
                    <x-ui.badge status="HILANG" :dot="true" />
                    <x-ui.badge variant="violet" rotate="left">Stiker Miring</x-ui.badge>
                </div>
            </x-ui.card>
        </section>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">3. Stat Cards</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-ui.stat-card color="yellow" icon="menu_book" badge="Koleksi" label="Total Koleksi" value="120" footerLeft="Judul Buku" footerRight="Detail" />
                <x-ui.stat-card color="mint" icon="inventory" badge="Eksemplar" label="Jumlah Stok Buku" value="480" footerLeft="Total Unit" footerRight="Detail" />
                <x-ui.stat-card color="sky" icon="badge" badge="Aktif" label="Jumlah Member" value="85" footerLeft="Anggota" footerRight="Detail" />
                <x-ui.stat-card color="pink" icon="sync_alt" badge="Sirkulasi" label="Sedang Dipinjam" value="19" footerLeft="Transaksi" footerRight="Detail" />
            </div>
        </section>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">4. Alert Flash</h3>
            <div class="space-y-3">
                <x-ui.alert type="success">Data buku berhasil disimpan ke sistem.</x-ui.alert>
                <x-ui.alert type="error">Terjadi kesalahan validasi atau akses ditolak.</x-ui.alert>
                <x-ui.alert type="warning">Periksa kembali batas waktu peminjaman.</x-ui.alert>
                <x-ui.alert type="info">Halaman ini hanya tersedia pada environment local.</x-ui.alert>
            </div>
        </section>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">5. Input &amp; Form</h3>
            <x-ui.card title="Contoh Form Input">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-ui.input name="sample_title" label="Judul Buku" placeholder="Masukkan judul buku..." value="Laskar Pelangi" :required="true" />
                    <x-ui.input name="sample_category" type="select" label="Kategori">
                        <option value="1">Fiksi</option>
                        <option value="2">Teknologi</option>
                    </x-ui.input>
                </div>
            </x-ui.card>
        </section>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">6. Tabel Zebra &amp; Hover Lilac</h3>
            <x-ui.table :headers="['Kode', 'Judul', 'Status', 'Aksi']">
                <tr>
                    <td class="font-mono text-xs">BK-001</td>
                    <td>Filosofi Teras</td>
                    <td><x-ui.badge status="DIPINJAM" /></td>
                    <td><x-ui.button size="sm" variant="secondary">Detail</x-ui.button></td>
                </tr>
                <tr>
                    <td class="font-mono text-xs">BK-002</td>
                    <td>Bumi Manusia</td>
                    <td><x-ui.badge status="DIKEMBALIKAN" /></td>
                    <td><x-ui.button size="sm" variant="secondary">Detail</x-ui.button></td>
                </tr>
                <tr>
                    <td class="font-mono text-xs">BK-003</td>
                    <td>Laut Bercerita</td>
                    <td><x-ui.badge status="TERLAMBAT" /></td>
                    <td><x-ui.button size="sm" variant="secondary">Detail</x-ui.button></td>
                </tr>
            </x-ui.table>
        </section>

        <section class="space-y-4">
            <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A]">7. Empty State</h3>
            <x-ui.empty-state icon="search_off" title="Data Tidak Ditemukan" description="Tidak ada arsip yang cocok dengan kata kunci pencarian Anda.">
                <x-ui.button variant="primary" size="sm">Reset Filter</x-ui.button>
            </x-ui.empty-state>
        </section>
    </div>
@endsection

@push('modals')
    <x-ui.modal id="uiKitModal" title="Konfirmasi Dialog">
        <p class="font-sans text-[14px] m-0">Ini adalah contoh modal Neo-Brutalist dengan hard shadow 10px dan window header.</p>
        <x-slot:footer>
            <x-ui.button variant="secondary" size="sm" data-modal-close="uiKitModal">Tutup</x-ui.button>
            <x-ui.button variant="primary" size="sm" data-modal-close="uiKitModal">Mengerti</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
@endpush
