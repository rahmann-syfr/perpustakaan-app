<?php

use App\Models\SystemSetting;
use App\Models\User;

it('mengembalikan 404 untuk rute pembayaran denda dan laporan analitik', function (string $role) {
    $user = User::factory()->{$role}()->create();

    $this->actingAs($user)->get('/denda')->assertNotFound();
    $this->actingAs($user)->get('/laporan')->assertNotFound();
})->with(['admin', 'petugas']);

it('menampilkan dashboard dengan 4 statistik wajib tanpa error', function (string $role) {
    $user = User::factory()->{$role}()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Total Koleksi', false)
        ->assertSee('Jumlah Stok Buku', false)
        ->assertSee('Jumlah Member', false)
        ->assertSee('Sedang Dipinjam', false)
        ->assertDontSee('Total Denda', false)
        ->assertDontSee('Denda Terbayar', false);
})->with(['admin', 'petugas']);

it('menyimpan pengaturan sistem', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('settings.update'), [
            'maksimal_hari_pinjam' => 14,
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

    expect(SystemSetting::get('nama_perpustakaan'))->toBe('Perpus Kota')
        ->and(SystemSetting::getInt('maksimal_perpanjangan', 0))->toBe(3)
        ->and(SystemSetting::getInt('durasi_perpanjangan', 0))->toBe(5);
});

it('halaman pengaturan admin tidak memuat bagian aturan denda', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('settings.index'))
        ->assertOk()
        ->assertDontSee('Aturan Denda', false)
        ->assertDontSee('Denda per Hari Terlambat', false)
        ->assertDontSee('Denda Buku Hilang', false)
        ->assertSee('Aturan Peminjaman', false)
        ->assertSee('Identitas Perpustakaan', false);
});

it('menyimpan pengaturan tidak mengubah nilai denda yang sudah ada di database', function () {
    SystemSetting::set('denda_per_hari', 2000);
    SystemSetting::set('denda_buku_hilang', 50000);

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('settings.update'), [
            'maksimal_hari_pinjam' => 10,
            'nama_perpustakaan' => 'Perpus Kota',
            'maksimal_buku_anggota' => 4,
            'hari_pengingat' => 2,
            'maksimal_perpanjangan' => 2,
            'durasi_perpanjangan' => 7,
        ])
        ->assertRedirect(route('settings.index'));

    expect(SystemSetting::getInt('denda_per_hari', 0))->toBe(2000)
        ->and(SystemSetting::getInt('denda_buku_hilang', 0))->toBe(50000);
});

it('menolak petugas menyimpan pengaturan sistem', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->put(route('settings.update'), [])
        ->assertRedirect(route('dashboard'));
});

it('petugas dapat membuka daftar data buku dan form tambah buku', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->get(route('books.index'))
        ->assertOk();

    $this->actingAs($petugas)
        ->get(route('books.create'))
        ->assertOk();
});
