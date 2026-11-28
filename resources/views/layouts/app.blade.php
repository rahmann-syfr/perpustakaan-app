@php
    $appName = \App\Models\SystemSetting::get('nama_perpustakaan') ?: 'PerpusApp';
    $user = \Illuminate\Support\Facades\Auth::user();
    $displayName = $user?->name ?? 'Pengguna';
    $displayRole = $user?->role?->label() ?? 'Tamu';
    $initial = strtoupper(mb_substr($displayName, 0, 1));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName . ' - Sistem Informasi Perpustakaan')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-grid-paper font-body-md text-[#0A0A0A] min-h-screen">
    <div id="sidebarBackdrop" class="fixed inset-0 z-40 hidden bg-[#0A0A0A]/50 lg:hidden" aria-hidden="true"></div>

    @include('layouts.sidebar')

    <div class="lg:pl-[260px] min-h-screen flex flex-col">
        <header class="fixed top-0 left-0 lg:left-[260px] right-0 h-[72px] bg-white border-b-[3px] border-[#0A0A0A] z-30 px-4 md:px-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button
                    type="button"
                    id="burgerBtn"
                    aria-label="Buka menu"
                    class="lg:hidden w-10 h-10 bg-[#F9C32E] border-[3px] border-[#0A0A0A] neo-shadow-sm flex items-center justify-center shrink-0 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[22px] text-[#0A0A0A]">menu</span>
                </button>

                <div class="hidden sm:flex w-6 h-6 bg-[#F9C32E] border-[2px] border-[#0A0A0A] rotate-45 items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[15px] text-[#0A0A0A] -rotate-45">star</span>
                </div>

                <h1 class="font-headline-lg text-[18px] md:text-[20px] uppercase tracking-wider font-extrabold text-[#0A0A0A] truncate m-0">
                    @yield('page-title', 'Sistem Informasi Perpustakaan')
                </h1>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @if ($user)
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 border-[3px] border-[#0A0A0A] bg-[#A9D9BC] py-1 px-2.5 neo-shadow-sm neo-shadow-hover transition-all text-[#0A0A0A] no-underline">
                        <div class="w-8 h-8 rounded-full border-[2px] border-[#0A0A0A] bg-[#F9C32E] flex items-center justify-center font-mono text-xs font-bold text-[#0A0A0A] shrink-0">
                            {{ $initial }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left min-w-0">
                            <span class="font-headline-sm text-xs font-bold text-[#0A0A0A] leading-tight truncate max-w-[140px]">{{ $displayName }}</span>
                            <span class="font-label-sm text-[9px] uppercase font-bold text-[#0A0A0A]/80">{{ $displayRole }}</span>
                        </div>
                    </a>
                @endif
            </div>
        </header>

        <main class="relative pt-[72px] flex-1 bg-grid-paper w-full">
            <div class="flex flex-col w-full p-6 md:p-8 space-y-6">
                @if (session('success'))
                    <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                @endif

                @if (session('error'))
                    <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
                @endif

                @if (session('warning'))
                    <x-ui.alert type="warning">{{ session('warning') }}</x-ui.alert>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('modals')

    @include('partials.scroll-memory')

    @stack('scripts')
</body>
</html>
