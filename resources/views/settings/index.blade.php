@extends('layouts.app')

@section('title', 'Pengaturan Sistem - PerpusApp')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Nilai internal yang tetap dibutuhkan validasi tanpa ditampilkan di UI --}}
        <input type="hidden" name="hari_pengingat" value="{{ old('hari_pengingat', $settings['hari_pengingat']) }}">

        {{-- Aturan Peminjaman --}}
        <x-ui.card :window="true" title="Aturan Peminjaman" bodyClass="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.input
                    name="maksimal_hari_pinjam"
                    type="number"
                    label="Maksimal Durasi Pinjam (Hari)"
                    :value="$settings['maksimal_hari_pinjam']"
                    min="1"
                    max="90"
                    :required="true"
                />

                <x-ui.input
                    name="maksimal_buku_anggota"
                    type="number"
                    label="Maksimal Buku per Anggota"
                    :value="$settings['maksimal_buku_anggota']"
                    min="1"
                    max="50"
                    :required="true"
                />

                <x-ui.input
                    name="maksimal_perpanjangan"
                    type="number"
                    label="Maksimal Perpanjangan (Kali)"
                    :value="$settings['maksimal_perpanjangan']"
                    min="0"
                    max="10"
                    :required="true"
                />

                <x-ui.input
                    name="durasi_perpanjangan"
                    type="number"
                    label="Durasi Perpanjangan (Hari)"
                    :value="$settings['durasi_perpanjangan']"
                    min="1"
                    max="90"
                    :required="true"
                />
            </div>
        </x-ui.card>

        {{-- Identitas Perpustakaan --}}
        <x-ui.card :window="true" title="Identitas Perpustakaan" bodyClass="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.input
                    name="nama_perpustakaan"
                    label="Nama Perpustakaan"
                    :value="$settings['nama_perpustakaan']"
                    :required="true"
                />

                <x-ui.input
                    name="email_perpustakaan"
                    type="email"
                    label="Email Perpustakaan"
                    :value="$settings['email_perpustakaan']"
                />

                <x-ui.input
                    name="alamat_perpustakaan"
                    label="Alamat Perpustakaan"
                    :value="$settings['alamat_perpustakaan']"
                />

                <x-ui.input
                    name="telepon_perpustakaan"
                    label="Telepon Perpustakaan"
                    :value="$settings['telepon_perpustakaan']"
                />
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-3">
            <x-ui.button variant="secondary" href="{{ route('dashboard') }}">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="save">Simpan Pengaturan</x-ui.button>
        </div>
    </form>
</div>
@endsection
