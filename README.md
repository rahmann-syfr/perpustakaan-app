# PerpusApp — Sistem Informasi Manajemen Perpustakaan

**PerpusApp** adalah aplikasi web manajemen perpustakaan berbasis **Laravel** dengan arsitektur **MVC (Model-View-Controller)**, **Eloquent ORM**, serta kontrol akses berbasis peran (**Role-Based Access Control**). Aplikasi ini dioperasikan oleh **Admin** dan **Petugas**, sementara anggota perpustakaan dikelola sebagai data pada tabel `members`.

---

## Tim Pengembang (Kelas 5D)
* **Nurul Annissa Azzahra**
* **Muhammad Syifaaur Rahman**
* **Muhammad Luthfi Ramadhan**

---

## Fitur & Hak Akses (Role)

### 1. Admin
* Login, Logout, & **Profil Saya**
* **Dashboard Statistik:** Menampilkan 4 statistik wajib (Total Koleksi/Judul Buku, Jumlah Stok Buku, Jumlah Member, Sedang Dipinjam), panel keterlambatan, dan grafik peminjaman.
* **Manajemen User:** CRUD penuh akun pengguna sistem (`admin` dan `petugas`).
* **Kategori Buku:** CRUD penuh kategori buku (tambah, lihat, edit, hapus).
* **Data Buku:** CRUD penuh koleksi buku, detail buku, upload sampul buku, serta sinkronisasi stok otomatis.
* **Data Anggota:** CRUD penuh data anggota beserta detail profil dan riwayat peminjamannya.
* **Sirkulasi:** Mencatat transaksi peminjaman, pengembalian, perpanjangan, perhitungan denda keterlambatan/buku hilang otomatis, filter transaksi, serta ekspor laporan PDF dan CSV.
* **Pengaturan Sistem:** Mengatur maksimal hari pinjam, maksimal buku aktif per anggota, dan batas perpanjangan.

### 2. Petugas
* Login, Logout, **Dashboard Statistik**, & **Profil Saya**.
* **Kategori Buku:** Melihat daftar kategori buku (*Read-Only*).
* **Data Buku:** CRUD penuh koleksi buku, detail buku, dan manajemen stok.
* **Data Anggota:** CRUD penuh data anggota dan detail riwayat peminjaman anggota.
* **Sirkulasi:** Mengelola transaksi peminjaman, pengembalian, perpanjangan, pencatatan denda keterlambatan/buku hilang, serta ekspor laporan sirkulasi PDF dan CSV.

---

## Akun Demo (Hasil Seeder)

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@perpus.com` | `password123` |
| **Petugas** | `petugas@perpus.com` | `password123` |

---

## Teknologi yang Digunakan
* **Backend:** PHP 8.4, Laravel Framework
* **Database:** MySQL (Eloquent ORM & Migrations)
* **Frontend:** Blade, Tailwind CSS v4, Bootstrap 5, tema PerpusApp kustom
* **Library Tambahan:** `barryvdh/laravel-dompdf` (Ekspor Laporan Sirkulasi PDF)

---

## Struktur Tabel Database
1. `users` — Akun pengguna yang dapat login (`admin`, `petugas`).
2. `categories` — Kategori buku (`hasMany` ke `books`).
3. `books` — Koleksi buku (`belongsTo` `categories`, memuat `total_stock`, `available_stock`, `fine_per_day`, dan `cover_image`).
4. `members` — Data anggota perpustakaan (`id`, `name`, `email`, `phone`, `address`, `timestamps`).
5. `borrowings` — Transaksi sirkulasi peminjaman dan pengembalian (`belongsTo` `members` sebagai peminjam, `users` sebagai petugas/admin, dan `books`, memuat `late_days` & `fine_amount`).
6. `system_settings` — Pengaturan konfigurasi perpustakaan termasuk tarif denda keterlambatan dan buku hilang.

---

## Cara Instalasi & Menjalankan Proyek

1. **Clone repository ini:**
   ```bash
   git clone https://github.com/rahmann-syfr/perpustakaan-app.git
   cd perpustakaan-app
   ```

2. **Install dependency PHP & Node.js:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Salin `.env`, generate key, dan jalankan migrasi + seeder:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```
