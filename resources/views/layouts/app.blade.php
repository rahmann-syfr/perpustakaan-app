@php
    $appName = \App\Models\SystemSetting::get('nama_perpustakaan') ?: 'PerpusApp';
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <script>
        (function () {
            try {
                if (localStorage.getItem('perpus-theme') === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName.' - Sistem Informasi Perpustakaan')</title>

    <!-- Bootstrap & Icons (kompatibel komponen halaman) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div id="app">
        @include('layouts.sidebar')

        <div id="main">
            <div class="perpus-topbar">
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="burger-btn" id="burgerBtn" aria-label="Buka menu">
                        <i class="bi bi-list fs-4"></i>
                    </a>
                    <div>
                        <h1 class="perpus-page-title">@yield('page-title')</h1>
                        <p class="perpus-page-title-sub mb-0 d-none d-md-block">Sistem Informasi Perpustakaan</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="{{ route('profile.show') }}" class="btn btn-sm btn-light-secondary d-none d-md-inline-flex align-items-center" title="Profil Saya">
                        <i class="bi bi-person-circle me-1"></i> Profil
                    </a>
                    <button type="button" class="btn btn-sm perpus-theme-toggle" id="themeToggle" aria-label="Ganti tema tampilan">
                        <i class="bi bi-moon-stars-fill" id="themeToggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="flex-grow-1">
                <section class="section">
                    @yield('content')
                </section>
            </div>

            <footer class="perpus-footer d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <p class="mb-0">&copy; {{ now()->year }} <strong>{{ $appName }}</strong> &mdash; Perpustakaan Digital</p>
                <p class="mb-0">Dibangun dengan <span class="text-danger"><i class="bi bi-heart-fill"></i></span> untuk edukasi</p>
            </footer>
        </div>
    </div>

    <!-- Wadah Modal Global (mencegah konflik z-index / backdrop) -->
    @stack('modals')

    <!-- Penampung Flash Message (agar bebas warning linter JS di VS Code) -->
    <div id="flash-messages"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         data-warning="{{ session('warning') }}"
         data-validation="{{ $errors->any() ? $errors->first() : '' }}"
         class="d-none">
    </div>

    <!-- Core Scripts: vanilla JS (tanpa jQuery/DataTables) untuk performa lebih baik -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('partials.scroll-memory')

    @stack('scripts')
</body>
</html>