<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'name' => 'Anggota Perpustakaan',
                'email' => 'anggota@perpus.com',
                'phone' => '081234567890',
                'address' => 'Jl. Pendidikan No. 1',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@perpus.com',
                'phone' => '081298765432',
                'address' => 'Jl. Merdeka No. 15',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@perpus.com',
                'phone' => '081345678901',
                'address' => 'Jl. Diponegoro No. 8',
            ],
        ];

        foreach ($members as $member) {
            Member::query()->updateOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'phone' => $member['phone'],
                    'address' => $member['address'],
                ]
            );
        }
    }
}
