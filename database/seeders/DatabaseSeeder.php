<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat akun staf (Admin & Petugas).
        $users = [
            ['name' => 'Admin Perpustakaan', 'email' => 'admin@perpus.com', 'role' => Role::Admin->value],
            ['name' => 'Petugas Perpustakaan', 'email' => 'petugas@perpus.com', 'role' => Role::Petugas->value],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => 'password123', // Password default
                ]
            );
        }

        // 2. Membuat kategori buku awal.
        $categories = ['Pemrograman Web', 'Sistem Basis Data', 'Fiksi', 'Sains & Teknologi'];

        foreach ($categories as $name) {
            Category::query()->updateOrCreate(['name' => $name], ['name' => $name]);
        }

        // 3. Membuat pengaturan sistem awal.
        $settings = [
            'denda_per_hari' => '2000',        // Denda Rp2.000 per hari terlambat.
            'maksimal_hari_pinjam' => '30',    // Durasi peminjaman 1-30 hari.
            'denda_buku_hilang' => '50000',    // Denda baku untuk buku yang hilang.
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::query()->updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }

        SystemSetting::flush();

        $this->call([
            MemberSeeder::class,
        ]);
    }
}
