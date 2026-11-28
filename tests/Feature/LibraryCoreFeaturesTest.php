<?php

use App\Enums\PaymentMethod;
use App\Models\Borrowing;
use App\Models\FinePayment;
use App\Models\SystemSetting;
use App\Models\User;

it('petugas dapat memperpanjang masa peminjaman', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->create([
        'due_date' => now()->startOfDay()->addDays(3),
        'extension_count' => 0,
    ]);

    $this->actingAs($petugas)
        ->post(route('borrowings.extend', $borrowing), ['days' => 7])
        ->assertRedirect(route('borrowings.index'));

    $borrowing->refresh();

    expect($borrowing->extension_count)->toBe(1)
        ->and($borrowing->due_date->greaterThan(now()->startOfDay()->addDays(3)))->toBeTrue();
});

it('menolak perpanjangan untuk peminjaman yang sudah terlambat', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->overdue(3)->create(['extension_count' => 0]);

    $this->actingAs($petugas)
        ->post(route('borrowings.extend', $borrowing), ['days' => 7])
        ->assertSessionHasErrors('status');

    expect($borrowing->refresh()->extension_count)->toBe(0);
});

it('menolak perpanjangan melebihi batas maksimum', function () {
    SystemSetting::set('maksimal_perpanjangan', 1);

    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->create([
        'due_date' => now()->startOfDay()->addDays(3),
        'extension_count' => 1,
    ]);

    $this->actingAs($petugas)
        ->post(route('borrowings.extend', $borrowing), ['days' => 7])
        ->assertSessionHasErrors('status');

    SystemSetting::flush();
});

it('tamu tidak dapat memperpanjang peminjaman', function () {
    $borrowing = Borrowing::factory()->create();

    $this->post(route('borrowings.extend', $borrowing), ['days' => 7])
        ->assertRedirect(route('login'));

    expect($borrowing->refresh()->extension_count)->toBe(0);
});

it('petugas dapat mencatat pembayaran denda', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->create(['fine_amount' => 50000]);

    $this->actingAs($petugas)
        ->post(route('fines.store', $borrowing), [
            'amount' => 20000,
            'method' => PaymentMethod::Cash->value,
            'reference' => 'TRX-001',
        ])
        ->assertRedirect();

    expect(FinePayment::query()->count())->toBe(1)
        ->and($borrowing->refresh()->outstandingFine())->toBe(30000);
});

it('menolak pembayaran yang melebihi sisa denda', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->create(['fine_amount' => 10000]);

    $this->actingAs($petugas)
        ->post(route('fines.store', $borrowing), [
            'amount' => 50000,
            'method' => PaymentMethod::Cash->value,
        ])
        ->assertSessionHasErrors('amount');

    expect(FinePayment::query()->count())->toBe(0);
});

it('menolak pembayaran pada transaksi tanpa denda', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->create(['fine_amount' => 0]);

    $this->actingAs($petugas)
        ->post(route('fines.store', $borrowing), [
            'amount' => 10000,
            'method' => PaymentMethod::Cash->value,
        ])
        ->assertSessionHasErrors('amount');
});

it('tamu tidak dapat mengakses halaman pembayaran denda', function () {
    $this->get(route('fines.index'))
        ->assertRedirect(route('login'));
});

it('pengguna dapat memperbarui profilnya sendiri', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->put(route('profile.update'), [
            'name' => 'Nama Baru Petugas',
            'email' => 'petugas.baru@perpus.test',
        ])
        ->assertRedirect(route('profile.show'));

    $petugas->refresh();

    expect($petugas->name)->toBe('Nama Baru Petugas')
        ->and($petugas->email)->toBe('petugas.baru@perpus.test');
});

it('menolak konfirmasi password yang tidak cocok pada profil', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->put(route('profile.update'), [
            'name' => $petugas->name,
            'email' => $petugas->email,
            'password' => 'rahasia123',
            'password_confirmation' => 'berbeda123',
        ])
        ->assertSessionHasErrors('password');
});

it('halaman profil menampilkan form edit profil', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertSee('Edit Profil')
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false);
});
