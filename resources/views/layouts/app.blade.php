<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PerpusApp - Sistem Informasi Perpustakaan')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Custom Mazer Style (Standar Mazer Terbaru) -->
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}"> -->

    <style>
        .table-action-buttons form {
            display: inline-block;
            margin: 0;
        }
        .table-action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Memanggil file sidebar -->
        @include('layouts.sidebar')

        <div id="main">
            <!-- Tombol Hamburger untuk versi Mobile -->
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none text-primary">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <div class="page-title mb-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3 class="mb-0">@yield('page-title')</h3>
                        </div>
                    </div>
                </div>
                
                <section class="section">
                    @yield('content')
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted mt-5">
                    <div class="float-start">
                        <p>2026 &copy; PerpusApp</p>
                    </div>
                    <div class="float-end">
                        <p>Dikembangkan untuk Sistem Informasi <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables Scripts -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Script Global -->
    <script>
        $(document).ready(function () {
            // Mengubah bahasa tabel menjadi Bahasa Indonesia secara otomatis
            if ($('#table1').length) {
                $('#table1').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                    },
                    "ordering": true,
                    "info": true,
                    "autoWidth": false
                });
            }

            // Fungsi untuk membuka tutup sidebar di mode mobile
            $('.burger-btn').on('click', function(e) {
                e.preventDefault();
                $('#sidebar').toggleClass('active');
            });
            $('.sidebar-hide').on('click', function(e) {
                e.preventDefault();
                $('#sidebar').removeClass('active');
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>