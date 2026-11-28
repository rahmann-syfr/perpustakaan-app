# PerpusApp — Sistem Informasi Manajemen Perpustakaan

**PerpusApp** adalah aplikasi web manajemen perpustakaan berbasis **Laravel** dengan arsitektur **MVC (Model-View-Controller)**, **Eloquent ORM**, serta kontrol akses berbasis peran (**Role-Based Access Control**). Aplikasi ini dirancang untuk dioperasikan oleh staf perpustakaan (**Admin** dan **Petugas**), sementara **anggota perpustakaan** dikelola sebagai data di dalam tabel `members`.

---

## Tim Pengembang (Kelas 5D)
* **Nurul Annissa Azzahra**
* **Muhammad Syifaaur Rahman**
* **Muhammad Luthfi Ramadhan**

---

## Fitur & Hak Akses (Role)

Sistem hanya memiliki dua aktor yang dapat login: **Admin** dan **Petugas**.

### 1. Admin
* **Login, Logout & Profil Saya:** Mengubah nama, email, dan password akun pribadi.
* **Dashboard Statistik:** Menampilkan 4 statistik wajib (Jumlah Judul Buku, Jumlah Stok Buku, Jumlah Member, Jumlah yang Sedang Dipinjam), informasi denda, keterlambatan, dan grafik tren.
* **Manajemen User:** CRUD penuh akun staf (`admin` dan `petugas`), dengan proteksi agar Admin tidak dapat menghapus akunnya sendiri atau menurunkan role Admin terakhir.
* **Kategori Buku:** CRUD penuh kategori buku (tambah, lihat, edit, hapus).
* **Data Buku:** CRUD penuh koleksi buku, detail buku, upload sampul buku, pencarian & filter kategori, serta sinkronisasi stok otomatis.
* **Data Anggota:** CRUD penuh data anggota (`members`) dengan 4 data utama (Nama Lengkap, Email, No HP, Alamat) serta detail daftar peminjaman anggota.
* **Sirkulasi & Denda:** Mencatat peminjaman, pengembalian, perpanjangan, buku hilang, pembayaran denda, serta ekspor laporan PDF dan CSV.
* **Laporan & Analitik:** Grafik tren bulanan, distribusi kategori, status transaksi, buku terpopuler, dan anggota teraktif.
* **Pengaturan Sistem:** Mengatur tarif denda per hari, denda buku hilang, maksimal hari pinjam, maksimal buku aktif per anggota, serta batas perpanjangan.

### 2. Petugas
* **Login, Logout, Dashboard & Profil Saya:** Akses penuh ke dashboard statistik dan pengelolaan profil pribadi.
* **Kategori Buku:** Melihat daftar kategori buku (*Read-Only*).
* **Data Buku, Data Anggota, Sirkulasi, Denda & Laporan:** Akses penuh operasional harian perpustakaan (tanpa akses ke Manajemen User dan Pengaturan Sistem).

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
1. `users` — Akun staf yang dapat login (`id`, `name`, `email`, `password`, `role` [`admin`, `petugas`], `timestamps`).
2. `categories` — Kategori buku (`id`, `name`, `timestamps`).
3. `books` — Koleksi buku (`id`, `category_id`, `title`, `author`, `publisher`, `isbn`, `published_year`, `total_stock`, `available_stock`, `fine_per_day`, `cover_image`, `timestamps`).
4. `members` — Data anggota perpustakaan (`id`, `name`, `email`, `phone`, `address`, `timestamps`).
5. `borrowings` — Transaksi sirkulasi peminjaman dan pengembalian (`id`, `member_id`, `admin_id`, `book_id`, `borrow_date`, `due_date`, `duration_days`, `extension_count`, `extended_at`, `return_date`, `late_days`, `status`, `fine_amount`, `timestamps`).
6. `fine_payments` — Catatan pembayaran denda (`id`, `borrowing_id`, `member_id`, `admin_id`, `amount`, `method`, `reference`, `note`, `timestamps`).
7. `system_settings` — Pengaturan konfigurasi perpustakaan (`id`, `setting_key`, `setting_value`, `timestamps`).

---

## Cara Instalasi & Menjalankan Proyek

1. **Clone repository ini:**
   ```bash
   git clone https://github.com/rahmann-syfr/perpustakaan-app.git
   cd perpustakaan-app
   ```

2. **Install dependency PHP:**
   ```bash
   composer install
   ```

3. **Salin file konfigurasi `.env` dan sesuaikan koneksi database:**
   ```bash
   cp .env.example .env
   ```
   Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada file `.env`.

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan migration dan seeder:**
   ```bash
   php artisan migrate --seed
   ```

6. **Install dependency frontend & build aset:**
   ```bash
   npm install
   npm run build
   ```

7. **Buat symlink storage (untuk sampul buku) & jalankan server lokal:**
   ```bash
   php artisan storage:link
   php artisan serve
   ```
   Buka `http://127.0.0.1:8000` di browser dan login menggunakan akun demo di atas.
