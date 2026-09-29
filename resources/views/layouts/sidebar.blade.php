<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="/"><h4>📚 PerpusApp</h4></a>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <!-- Profil User -->
                <li class="sidebar-title">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md bg-primary me-3">
                            <span class="avatar-content text-uppercase">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h6 class="mb-0 text-gray-600">{{ Auth::user()->name }}</h6>
                            <span class="badge bg-light-success mt-1">{{ strtoupper(Auth::user()->role) }}</span>
                        </div>
                    </div>
                </li>
                <hr>

                <li class="sidebar-title">Menu Utama</li>
                
                <!-- Menu Dashboard (Semua Role) -->
                <li class="sidebar-item {{ Request::is('/') ? 'active' : '' }}">
                    <a href="/" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Menu Katalog (Semua Role - Posisikan di LUAR pengecekan role admin) -->
                <li class="sidebar-item {{ Request::is('katalog') ? 'active' : '' }}">
                    <a href="/katalog" class='sidebar-link'>
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <span>Katalog Buku</span>
                    </a>
                </li>

                <!-- Menu Khusus Admin & Superadmin -->
                @if(Auth::user()->role != 'anggota')
                <li class="sidebar-item {{ Request::is('categories*') ? 'active' : '' }}">
                    <a href="/categories" class='sidebar-link'>
                        <i class="bi bi-tags-fill"></i>
                        <span>Kategori Buku</span>
                    </a>
                </li>

                <li class="sidebar-item {{ Request::is('books*') ? 'active' : '' }}">
                    <a href="/books" class='sidebar-link'>
                        <i class="bi bi-book-half"></i>
                        <span>Data Buku</span>
                    </a>
                </li>

                <li class="sidebar-item {{ Request::is('borrowings*') ? 'active' : '' }}">
                    <a href="/borrowings" class='sidebar-link'>
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Sirkulasi</span>
                    </a>
                </li>
                @endif

                <!-- Logout -->
                <li class="sidebar-item mt-5">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light-danger w-100 d-flex justify-content-start align-items-center">
                            <i class="bi bi-box-arrow-left me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>