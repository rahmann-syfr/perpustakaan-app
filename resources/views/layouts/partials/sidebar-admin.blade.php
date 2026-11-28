<div class="flex flex-col gap-1.5">
    <div class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A] px-1">Menu Utama</div>
    <div class="flex flex-col gap-1.5">
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('dashboard'),
            'icon' => 'dashboard',
            'label' => 'Dashboard',
            'active' => request()->routeIs('dashboard'),
        ])
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('profile.show'),
            'icon' => 'account_circle',
            'label' => 'Profil Saya',
            'active' => request()->routeIs('profile.*'),
        ])
    </div>
</div>

<div class="flex flex-col gap-1.5">
    <div class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A] px-1">Layanan &amp; Koleksi</div>
    <div class="flex flex-col gap-1.5">
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('categories.index'),
            'icon' => 'bookmarks',
            'label' => 'Kategori Buku',
            'active' => request()->routeIs('categories.*'),
        ])
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('books.index'),
            'icon' => 'menu_book',
            'label' => 'Data Buku',
            'active' => request()->routeIs('books.*'),
        ])
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('members.index'),
            'icon' => 'badge',
            'label' => 'Data Anggota',
            'active' => request()->routeIs('members.*'),
        ])
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('borrowings.index'),
            'icon' => 'sync_alt',
            'label' => 'Sirkulasi',
            'active' => request()->routeIs('borrowings.*'),
        ])
    </div>
</div>

<div class="flex flex-col gap-1.5">
    <div class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A] px-1">Administrasi</div>
    <div class="flex flex-col gap-1.5">
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('users.index'),
            'icon' => 'group',
            'label' => 'Manajemen User',
            'active' => request()->routeIs('users.*'),
        ])
        @include('layouts.partials.sidebar-nav-item', [
            'href' => route('settings.index'),
            'icon' => 'tune',
            'label' => 'Pengaturan Sistem',
            'active' => request()->routeIs('settings.*'),
        ])
    </div>
</div>
