<?php

use App\Models\SystemSetting;
use App\Models\User;

it('menampilkan halaman laporan analitik untuk petugas', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->get(route('analytics.index'))
        ->assertOk()
        ->assertSee('Tren Peminjaman Bulanan');
});

it('menolak akses laporan untuk anggota', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->get(route('analytics.index'))
        ->assertRedirect(route('dashboard'));
});

it('menyimpan pengaturan sistem', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('settings.update'), [
            'denda_per_hari' => 2500,
            'maksimal_hari_pinjam' => 14,
            'denda_buku_hilang' => 75000,
            'nama_perpustakaan' => 'Perpus Kota',
            'alamat_perpustakaan' => 'Jl. Merdeka 1',
            'email_perpustakaan' => 'info@perpus.test',
            'telepon_perpustakaan' => '021-123456',
            'maksimal_buku_anggota' => 5,
            'hari_pengingat' => 2,
            'maksimal_perpanjangan' => 3,
            'durasi_perpanjangan' => 5,
        ])
        ->assertRedirect(route('settings.index'));

    expect(SystemSetting::getInt('denda_per_hari', 0))->toBe(2500)
        ->and(SystemSetting::get('nama_perpustakaan'))->toBe('Perpus Kota')
        ->and(SystemSetting::getInt('maksimal_perpanjangan', 0))->toBe(3)
        ->and(SystemSetting::getInt('durasi_perpanjangan', 0))->toBe(5);
});

it('menolak anggota menyimpan pengaturan sistem', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->put(route('settings.update'), [])
        ->assertRedirect(route('dashboard'));
});

it('anggota tidak dapat membuka daftar data buku', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->get(route('books.index'))
        ->assertRedirect(route('dashboard'));
});

it('anggota tidak dapat membuka form tambah buku', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->get(route('books.create'))
        ->assertRedirect(route('dashboard'));
});
