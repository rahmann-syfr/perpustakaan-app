<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Akun Superadmin
        User::create([
            'name' => 'Superadmin Perpus',
            'email' => 'superadmin@perpus.com',
            'password' => Hash::make('password123'), // Password default
            'role' => 'superadmin',
        ]);

        // 2. Membuat Akun Admin / Petugas
        User::create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 3. Membuat Akun Anggota
        User::create([
            'name' => 'Mahasiswa Anggota',
            'email' => 'anggota@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
        ]);

        // 4. Membuat Kategori Buku Awal
        $categories = ['Pemrograman Web', 'Sistem Basis Data', 'Fiksi', 'Sains & Teknologi'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }

        // 5. Membuat Pengaturan Sistem Awal (System Settings)
        SystemSetting::create([
            'setting_key' => 'denda_per_hari',
            'setting_value' => '2000' // Denda Rp2.000 per hari terlambat
        ]);
        
        SystemSetting::create([
            'setting_key' => 'maksimal_hari_pinjam',
            'setting_value' => '7' // Maksimal pinjam 7 hari
        ]);
    }
}