@extends('layouts.app')

@section('title', 'Profil Saya - PerpusApp')
@section('page-title', 'Profil Saya')

@section('content')
@php
    $initial = strtoupper(mb_substr($user->name, 0, 1));
    $roleVariant = $user->role === \App\Enums\Role::Admin ? 'violet' : 'info';
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Kartu Identitas Akun --}}
    <x-ui.card :window="true" title="profile.info" bodyClass="p-6 bg-[#FFF6E0]">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full border-[3px] border-[#0A0A0A] bg-[#F9C32E] neo-shadow-md flex items-center justify-center font-display-md text-[28px] font-extrabold text-[#0A0A0A] shrink-0">
                    {{ $initial }}
                </div>
                <div>
                    <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">{{ $user->name }}</h2>
                    <p class="font-mono text-[12px] text-[#4f4633] mt-0.5 mb-0">{{ $user->email }}</p>
                </div>
            </div>
            <x-ui.badge :variant="$roleVariant">{{ strtoupper($user->role->label()) }}</x-ui.badge>
        </div>
    </x-ui.card>

    {{-- Form Informasi Akun & Ubah Password --}}
    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <x-ui.card title="Edit Profil" bodyClass="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.input
                    name="name"
                    label="Nama Lengkap"
                    :value="$user->name"
                    :required="true"
                />
                <x-ui.input
                    name="email"
                    type="email"
                    label="Email"
                    :value="$user->email"
                    :required="true"
                />
            </div>
        </x-ui.card>

        <x-ui.card title="Ubah Password" rightLabel="Opsional" bodyClass="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.input
                    name="password"
                    type="password"
                    label="Password Baru"
                    placeholder="Kosongkan jika tidak diubah"
                    autocomplete="new-password"
                />
                <x-ui.input
                    name="password_confirmation"
                    type="password"
                    label="Konfirmasi Password Baru"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                />
            </div>
        </x-ui.card>

        <div class="flex items-center justify-between gap-3">
            <x-ui.button variant="secondary" icon="arrow_back" href="{{ route('dashboard') }}">Kembali</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="save">Simpan Profil</x-ui.button>
        </div>
    </form>
</div>
@endsection
