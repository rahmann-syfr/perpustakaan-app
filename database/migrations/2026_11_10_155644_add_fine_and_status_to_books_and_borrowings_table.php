<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom denda per buku di tabel books
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('fine_per_day', 10, 2)->default(1000)->after('available_stock'); // Default denda Rp1.000/hari
        });

        // 2. Tambah kolom durasi/hari pinjam dan status 'hilang' di tabel borrowings
        Schema::table('borrowings', function (Blueprint $table) {
            $table->integer('duration_days')->default(7)->after('due_date'); // Default durasi peminjaman 7 hari
            // Ubah/perluas enum status agar menerima 'lost' (hilang)
            // Catatan: Jika menggunakan SQLite/MySQL, pastikan kolom status mendukung string/enum baru
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('fine_per_day');
        });

        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn('duration_days');
        });
    }
};