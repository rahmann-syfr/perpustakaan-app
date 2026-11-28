@extends('layouts.guest')

@php
    $appName = \App\Models\SystemSetting::get('nama_perpustakaan') ?: 'PerpusApp';
@endphp

@section('title', 'Masuk - ' . $appName)

@section('content')
    <div class="w-full max-w-[960px] mx-auto bg-white border-[3px] border-[#0A0A0A] neo-shadow-xl grid grid-cols-1 md:grid-cols-2">
        {{-- Left Half: Violet Branding Panel --}}
        <div class="bg-[#7B4DFF] text-white border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#0A0A0A] p-8 md:p-12 flex flex-col justify-between min-h-[380px] md:min-h-[560px] relative overflow-hidden">
            <div class="flex flex-col items-start gap-6 relative z-10">
                <div class="inline-block bg-[#F9C32E] text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-md px-4 py-2 -rotate-3">
                    <span class="font-mono text-[12px] font-bold uppercase tracking-wider">SELAMAT DATANG</span>
                </div>
                <div class="flex flex-col gap-3 mt-2">
                    <h1 class="font-display-lg text-[40px] md:text-[48px] font-extrabold text-white tracking-tight leading-none m-0">
                        {{ $appName }}
                    </h1>
                    <p class="font-mono text-[14px] font-bold text-[#e7deff] max-w-[320px] m-0">
                        Sistem Informasi Perpustakaan Sederhana
                    </p>
                </div>
            </div>

            <div class="mt-10 pt-6 relative z-10 flex flex-col items-start gap-3">
                <div class="w-56 h-10 bg-[#ffdad6] text-[#93000a] border-[3px] border-[#0A0A0A] neo-shadow-md flex items-center justify-between px-4 -rotate-2">
                    <span class="font-mono text-[10px] font-bold uppercase">ARSIP-01</span>
                    <span class="font-mono text-[10px] font-bold uppercase">KATALOG</span>
                </div>
                <div class="w-64 h-10 bg-[#A9D9BC] text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-md flex items-center justify-between px-4 rotate-1 ml-4">
                    <span class="font-mono text-[10px] font-bold uppercase">ARSIP-02</span>
                    <span class="font-mono text-[10px] font-bold uppercase">SIRKULASI</span>
                </div>
                <div class="w-60 h-10 bg-[#F9C32E] text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-md flex items-center justify-between px-4 -rotate-1 ml-1">
                    <span class="font-mono text-[10px] font-bold uppercase">ARSIP-03</span>
                    <span class="font-mono text-[10px] font-bold uppercase">PUSTAKAWAN</span>
                </div>
            </div>
        </div>

        {{-- Right Half: Login Form --}}
        <div class="bg-white p-8 md:p-12 flex flex-col justify-center">
            <div class="mb-8">
                <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633] tracking-wider block mb-2">OTENTIKASI STAF</span>
                <h2 class="font-display-md text-[32px] md:text-[36px] font-extrabold text-[#0A0A0A] leading-tight m-0">
                    Masuk ke Akun
                </h2>
            </div>

            <form action="{{ route('login.store') }}" method="POST" class="flex flex-col gap-5" id="login-form">
                @csrf

                <div class="flex flex-col gap-2">
                    <label class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A]" for="email-input">
                        EMAIL
                    </label>
                    <input
                        id="email-input"
                        name="email"
                        type="email"
                        autocomplete="email"
                        value="{{ old('email') }}"
                        placeholder="admin@perpus.com"
                        required
                        autofocus
                        class="w-full bg-white text-[#0A0A0A] font-sans text-[15px] font-medium px-4 py-3 border-[3px] border-[#0A0A0A] neo-shadow-md focus:outline-none focus:border-[#7B4DFF] focus:shadow-[4px_4px_0px_#7B4DFF] transition-colors placeholder:text-[#817661]"
                    />
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A]" for="password-input">
                        PASSWORD
                    </label>
                    <div class="flex w-full">
                        <input
                            id="password-input"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                            class="w-full bg-white text-[#0A0A0A] font-sans text-[15px] font-medium px-4 py-3 border-[3px] border-r-0 border-[#0A0A0A] neo-shadow-md focus:outline-none focus:border-[#7B4DFF] focus:shadow-[4px_4px_0px_#7B4DFF] transition-colors placeholder:text-[#817661]"
                        />
                        <button
                            id="toggle-password"
                            type="button"
                            aria-label="Tampilkan atau sembunyikan password"
                            class="w-12 shrink-0 bg-white text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-md flex items-center justify-center hover:bg-[#F9C32E] neo-shadow-active transition-all cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                        <input
                            id="remember-me"
                            name="remember"
                            type="checkbox"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-5 h-5 accent-[#7B4DFF] border-[3px] border-[#0A0A0A] cursor-pointer"
                        />
                        <span class="font-sans text-[14px] font-medium text-[#0A0A0A]">Ingat saya</span>
                    </label>
                </div>

                @if ($errors->any() || session('error'))
                    <div class="bg-[#FF7A9C] text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-sm px-4 py-3 flex items-center gap-3" role="alert">
                        <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
                        <span class="font-sans text-[14px] font-bold">{{ $errors->first() ?: session('error') }}</span>
                    </div>
                @endif

                <button
                    type="submit"
                    class="w-full bg-[#F9C32E] text-[#0A0A0A] font-mono text-[14px] font-bold uppercase tracking-[0.04em] py-4 px-6 border-[3px] border-[#0A0A0A] neo-shadow-md neo-shadow-hover neo-shadow-active transition-all cursor-pointer"
                >
                    MASUK
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const toggleBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password-input');
            const eyeIcon = document.getElementById('eye-icon');

            if (toggleBtn && passwordInput && eyeIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }
        })();
    </script>
@endpush
