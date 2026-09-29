<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Perpustakaan')</title>
    
    <!-- Load CSS Mazer dari folder public/assets -->
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
</head>
<body>
    <div id="app">
        <!-- Memanggil komponen Sidebar -->
        @include('layouts.sidebar')
        
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            
            <div class="page-heading">
                <h3>@yield('page-title')</h3>
            </div>
            
            <div class="page-content">
                <!-- Konten utama akan disisipkan di sini -->
                @yield('content')
            </div>

            <!-- Memanggil komponen Footer -->
            @include('layouts.footer')
        </div>
    </div>
    
    <!-- Load JS Mazer -->
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
</body>
</html>