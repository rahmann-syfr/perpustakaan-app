<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
            User::create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'petugas@perpus.com',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
        ]);
    }
}
