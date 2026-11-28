@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $appName = \App\Models\SystemSetting::get('nama_perpustakaan') ?: 'PerpusApp';
    $userName = $user?->name ?? 'Preview UI';
    $userEmail = $user?->email ?? 'preview@perpus.local';
    $roleValue = $user?->role?->value ?? 'admin';
    $roleLabel = strtoupper($user?->role?->label() ?? 'Admin');
    $initial = strtoupper(mb_substr($userName, 0, 1));
    $roleBadgeBg = match ($roleValue) {
        'admin' => 'bg-[#7B4DFF] text-white',
        'petugas' => 'bg-[#4FA8FF] text-[#0A0A0A]',
        default => 'bg-[#C4F04D] text-[#0A0A0A]',
    };
@endphp

<aside
    id="sidebar"
    aria-label="Navigasi utama"
    class="fixed left-0 top-0 h-screen w-[260px] bg-[#A9D9BC] border-r-[3px] border-[#0A0A0A] z-50 flex flex-col justify-between -translate-x-full lg:translate-x-0 transition-transform duration-200"
>
    <div class="p-4 flex flex-col gap-4 min-h-0 flex-1 overflow-y-auto perpus-sidebar__nav">
        <div class="flex items-center justify-between gap-2 pb-3 border-b-[3px] border-[#0A0A0A]">
            <a href="{{ \Illuminate\Support\Facades\Route::has('dashboard') ? route('dashboard') : '/' }}" class="flex items-center gap-3 min-w-0 text-decoration-none">
                <div class="w-11 h-11 border-[3px] border-[#0A0A0A] bg-white flex items-center justify-center shrink-0 neo-shadow-sm">
                    <span class="material-symbols-outlined text-[24px] text-[#0A0A0A]">local_library</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-headline-md text-[20px] leading-none font-extrabold tracking-tight text-[#0A0A0A] truncate">{{ $appName }}</span>
                    <span class="font-label-sm text-[9px] uppercase tracking-wider text-[#0A0A0A] font-bold mt-1">Sistem Perpustakaan</span>
                </div>
            </a>
            <button type="button" class="lg:hidden sidebar-hide w-8 h-8 bg-white border-[2px] border-[#0A0A0A] flex items-center justify-center shrink-0 cursor-pointer" aria-label="Tutup menu">
                <span class="material-symbols-outlined text-[18px] text-[#0A0A0A]">close</span>
            </button>
        </div>

        <div class="bg-[#F9C32E] border-[3px] border-[#0A0A0A] neo-shadow-md p-3">
            <div class="flex items-center gap-2.5">
                <div class="w-11 h-11 rounded-full border-[2px] border-[#0A0A0A] bg-white flex items-center justify-center font-mono text-base font-bold text-[#0A0A0A] shrink-0">
                    {{ $initial }}
                </div>
                <div class="flex flex-col min-w-0 overflow-hidden">
                    <span class="font-headline-sm text-sm font-bold text-[#0A0A0A] truncate leading-tight" title="{{ $userName }}">{{ $userName }}</span>
                    <span class="font-label-sm text-[10px] text-[#0A0A0A]/80 truncate">{{ $userEmail }}</span>
                    <div class="mt-1">
                        <span class="inline-block {{ $roleBadgeBg }} font-mono text-[9px] uppercase px-2 py-0.5 border-[2px] border-[#0A0A0A] font-bold leading-none">
                            {{ $roleLabel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex flex-col gap-4">
            @if ($roleValue === 'admin')
                @include('layouts.partials.sidebar-admin')
            @elseif ($roleValue === 'petugas')
                @include('layouts.partials.sidebar-petugas')
            @else
                @include('layouts.partials.sidebar-anggota')
            @endif
        </nav>
    </div>

    @if ($user)
        <div class="p-4 border-t-[3px] border-[#0A0A0A] bg-[#A9D9BC]">
            <form action="{{ route('logout') }}" method="POST" class="no-spinner">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full py-2.5 bg-[#FF7A9C] border-[3px] border-[#0A0A0A] neo-shadow-md neo-shadow-hover neo-shadow-active text-[#0A0A0A] font-mono text-xs uppercase font-bold tracking-wider cursor-pointer transition-all">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    @endif
</aside>
