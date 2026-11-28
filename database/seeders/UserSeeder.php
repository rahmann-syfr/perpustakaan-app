<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Perpustakaan',
                'email' => 'admin@perpus.com',
                'role' => Role::Admin->value,
            ],
            [
                'name' => 'Petugas Perpustakaan',
                'email' => 'petugas@perpus.com',
                'role' => Role::Petugas->value,
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => 'password123',
                ]
            );
        }
    }
}
