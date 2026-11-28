<div class="flex flex-col gap-1.5">
    <div class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A] px-1">Menu Anggota</div>
    <div class="flex flex-col gap-1.5">
        @if (\Illuminate\Support\Facades\Route::has('dashboard'))
            @include('layouts.partials.sidebar-nav-item', [
                'href' => route('dashboard'),
                'icon' => 'dashboard',
                'label' => 'Dashboard',
                'active' => request()->routeIs('dashboard'),
            ])
        @endif
        @if (\Illuminate\Support\Facades\Route::has('books.index'))
            @include('layouts.partials.sidebar-nav-item', [
                'href' => route('books.index'),
                'icon' => 'menu_book',
                'label' => 'Katalog Buku',
                'active' => request()->routeIs('books.*'),
            ])
        @endif
        @if (\Illuminate\Support\Facades\Route::has('borrowings.index'))
            @include('layouts.partials.sidebar-nav-item', [
                'href' => route('borrowings.index'),
                'icon' => 'history',
                'label' => 'Riwayat Pinjam',
                'active' => request()->routeIs('borrowings.*'),
            ])
        @endif
        @if (\Illuminate\Support\Facades\Route::has('profile.show'))
            @include('layouts.partials.sidebar-nav-item', [
                'href' => route('profile.show'),
                'icon' => 'account_circle',
                'label' => 'Profil Saya',
                'active' => request()->routeIs('profile.*'),
            ])
        @endif
    </div>
</div>
