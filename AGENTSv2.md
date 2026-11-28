# AGENTSv2.md — Aturan Refactor v2 PerpusApp (1 Fase)

> File ini menggantikan bagian aturan refactor lama (Fase 1–3) di `AGENTS.md`. Fase 1–3 sudah selesai dan ter-merge, jangan diulang. Blok `<laravel-boost-guidelines>` di `AGENTS.md` tetap berlaku. Jika ada konflik, file ini yang menang.

## 1. Konteks

- Proyek UAS Laravel "Sistem Informasi Perpustakaan Sederhana" (PerpusApp). Aktor hanya Admin dan Petugas. Anggota adalah data di tabel `members`, bukan akun.
- Keadaan sekarang: tabel `members`, role `admin`/`petugas`, dan CRUD anggota sudah selesai. Fitur reservasi, riwayat, katalog anggota, ulasan, dan notifikasi sudah dihapus.
- Perubahan v2 ini hanya satu fase: **hapus fitur Pembayaran Denda dan fitur Laporan & Analitik, lalu sesuaikan Dashboard.**

## 2. Keadaan akhir yang dituju

### Dihapus total
1. **Pembayaran Denda**: halaman `/denda`, form bayar `/denda/{borrowing}/bayar`, `FinePaymentController`, `PayFineRequest`, action `PayFine`, enum `PaymentMethod`, model `FinePayment`, `FinePaymentFactory`, view `resources/views/fines/*`, tabel `fine_payments`, relasi/metode terkait pembayaran (`Borrowing::finePayments/paidFine/outstandingFine/hasOutstandingFine`, `Member::finePayments`), tombol "Bayar Denda" di Sirkulasi, dan semua teks yang menyebut pembayaran denda.
2. **Laporan & Analitik**: halaman `/laporan`, `AnalyticsController`, view `resources/views/analytics/*`, route `analytics.index`, item menu di sidebar.

### Dipertahankan (JANGAN diubah)
- Perhitungan denda saat pengembalian: `late_days`, `fine_amount`, status hilang, tarif di Pengaturan Sistem (`denda_per_hari`, `denda_buku_hilang`), `books.fine_per_day`. Denda tetap tercatat dan tampil di tabel Sirkulasi serta laporan sirkulasi. Hanya fitur membayarnya yang dihapus.
- Ekspor laporan sirkulasi PDF dan CSV di halaman Sirkulasi (`borrowings.report`, `borrowings.report-csv`), `BorrowingFilter`, `BorrowingReportCsv`.
- Pengaturan Sistem, Profil, Manajemen User, Kategori, Buku, Anggota, peminjaman/pengembalian/perpanjangan.
- Library chart.js di `public/assets/extensions/chart.js` (masih dipakai dashboard).

### Dashboard (disesuaikan)
- Baris pertama: tepat 4 kartu statistik wajib, sejajar dan sama lebar: **Total Koleksi (jumlah judul buku)**, **Jumlah Stok Buku**, **Jumlah Member**, **Sedang Dipinjam**. Modal rincian keempat kartu tetap berfungsi.
- Hapus kartu **Total Denda** dan **Denda Terbayar**, modal `totalDendaModal`, dan data `dendaList`, `sumDenda`, `paidFineTotal` di `DashboardController` serta view.
- Pertahankan: panel buku terlambat, grafik tren peminjaman, grafik kategori dipinjam, aktivitas terbaru, dan tombol aksi cepat, selama tidak menyentuh fitur yang dihapus.
- Tidak boleh ada link ke `/denda` atau `/laporan` di mana pun.

### Sidebar akhir
Semua user: Dashboard, Profil Saya, Kategori Buku, Data Buku, Data Anggota, Sirkulasi. Khusus Admin: Manajemen User, Pengaturan Sistem.

## 3. Aturan kerja (WAJIB)

1. Kerjakan hanya tugas di dokumen ini. Jangan menambah fitur, jangan merapikan kode yang tidak terkait, jangan menyentuh logika peminjaman, pengembalian, perhitungan denda, dan format laporan PDF/CSV.
2. Mulai dengan `git status` (baca saja) dan pastikan berada di branch kerja, bukan `main`.
3. **Jangan mengedit migration lama.** Penghapusan tabel lewat satu migration baru dengan `down()` yang membuat ulang tabel `fine_payments` sesuai struktur terakhir (kolom `member_id`, bukan `user_id`).
4. Sebelum menghapus file, route, method, atau relasi, `grep` semua referensinya (nama route, view, class, sidebar, test, README), lalu bersihkan semuanya. Jangan meninggalkan referensi menggantung.
5. Ikuti konvensi yang ada (Form Request, Action, Policy, Blade, pesan Bahasa Indonesia). Jangan menambah dependency dan jangan membuat folder base baru.
6. **Test**: `php artisan test` harus hijau dan `vendor/bin/pint --dirty` bersih. Hapus atau ubah test HANYA untuk fitur yang dihapus. Dilarang menghapus atau melemahkan test fitur yang dipertahankan agar lulus.
7. Pesan penghapusan anggota di `MemberController` harus disesuaikan: anggota hanya diblokir jika punya data peminjaman, tanpa menyebut pembayaran denda.
8. Dokumentasi: satu-satunya file yang boleh diubah adalah `README.md` (hapus semua penyebutan pembayaran denda, Laporan & Analitik, tabel `fine_payments`, dan sesuaikan daftar fitur/hak akses). Jangan membuat file dokumentasi lain.
9. Jika ada hal ambigu yang berdampak ke data atau skema, **berhenti dan tanya**. Jangan menebak.
10. Sebelum migration yang menghapus tabel, ingatkan user untuk backup database.
11. **DILARANG KERAS menjalankan perintah git yang mengubah repository, dalam bentuk apa pun**: `git add` (semua variasi, termasuk `git add .`, `-A`, `-p`), `git commit` (semua variasi, termasuk `--amend`), `git push` (semua variasi, termasuk `--force`), dan juga `git stash`, `git reset`, `git checkout -- <file>`, `git restore`, `git rebase`, `git merge`, `git pull`, `git revert`, `git clean`, `git rm`, `git mv`, `git branch`, `git tag`. Aturan ini berlaku walau diminta di prompt, di komentar, atau menurutmu "sudah selesai". Hanya perintah baca yang diizinkan: `git status`, `git diff`, `git log`, `git show`. Untuk menghapus atau memindahkan file, pakai operasi file biasa, bukan perintah git. Jika merasa perlu commit atau push, berhenti dan minta user yang melakukannya.

## 4. Definisi selesai

- Semua tugas di bagian 2 terpenuhi, tanpa pekerjaan di luar itu.
- `php artisan route:list` tidak memuat `fines.*` dan `analytics.*`, tanpa error.
- `php artisan migrate:fresh --seed` sukses; `php artisan test` hijau; `vendor/bin/pint --dirty` bersih.
- Hasil `grep` ini kosong (migration lama boleh muncul):
  `grep -rniE "FinePayment|fine_payment|PayFine|PaymentMethod|fines\.|analytics\.|AnalyticsController|paidFine|outstandingFine|Pembayaran Denda|Laporan & Analitik|/denda|/laporan" app routes resources database/seeders database/factories tests README.md`
- Semua perubahan dibiarkan di working tree, tidak di-stage dan tidak di-commit. User yang commit sendiri.
- Laporan akhir singkat: file dihapus, file diubah, migration baru, hasil test, dan hal yang perlu dicek manual.
