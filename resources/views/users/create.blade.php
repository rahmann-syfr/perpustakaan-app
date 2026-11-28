@extends('layouts.app')

@section('title', 'Tambah User - PerpusApp')
@section('page-title', 'Tambah Data Pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-ui.card :window="true" title="users.create" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b-[3px] border-[#0A0A0A]">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Form Tambah User Baru</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Buat akun baru untuk Admin atau Petugas perpustakaan.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('users.index') }}">
                Kembali
            </x-ui.button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
            @csrf

            <x-ui.input name="name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" :required="true" />

            <x-ui.input name="email" type="email" label="Alamat Email" placeholder="nama@perpus.com" :required="true" />

            <div class="space-y-1.5">
                <label for="passwordInput" class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A] block">
                    Password <span class="text-[#ba1a1a]">*</span>
                </label>
                <div class="flex w-full">
                    <input
                        type="password"
                        id="passwordInput"
                        name="password"
                        minlength="8"
                        placeholder="Minimal 8 karakter"
                        required
                        class="w-full bg-white text-[#0A0A0A] font-sans text-[15px] font-medium px-4 py-3 border-[3px] border-r-0 {{ $errors->has('password') ? 'border-[#FF7A9C]' : 'border-[#0A0A0A]' }} neo-shadow-md focus:outline-none focus:border-[#7B4DFF]"
                    />
                    <button
                        type="button"
                        data-toggle-password="passwordInput"
                        aria-label="Lihat atau sembunyikan password"
                        class="w-12 shrink-0 bg-white text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-md flex items-center justify-center hover:bg-[#F9C32E] cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="font-mono text-[11px] font-bold text-[#93000a] bg-[#ffdad6] border-[2px] border-[#0A0A0A] px-2.5 py-1 m-0">{{ $message }}</p>
                @enderror
            </div>

            <x-ui.input name="role" type="select" label="Role / Hak Akses" :required="true">
                <option value="">-- Pilih Role --</option>
                @foreach ($assignableRoles as $assignableRole)
                    <option value="{{ $assignableRole }}" {{ old('role') == $assignableRole ? 'selected' : '' }}>
                        {{ \App\Enums\Role::from($assignableRole)->label() }}
                    </option>
                @endforeach
            </x-ui.input>

            <div class="flex items-center justify-end gap-3 pt-4 border-t-[3px] border-[#0A0A0A]">
                <x-ui.button variant="secondary" href="{{ route('users.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="save">Simpan User</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.getAttribute('data-toggle-password'));
            const icon = btn.querySelector('.material-symbols-outlined');
            if (!input || !icon) return;
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            icon.textContent = isPass ? 'visibility_off' : 'visibility';
        });
    });
</script>
@endpush
