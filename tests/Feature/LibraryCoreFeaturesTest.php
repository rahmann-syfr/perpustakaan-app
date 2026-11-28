<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
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

it('pengembalian terlambat tetap menghitung fine_amount', function () {
    $petugas = User::factory()->petugas()->create();
    $book = Book::factory()->create(['fine_per_day' => 2000, 'total_stock' => 2, 'available_stock' => 1]);
    $borrowing = Borrowing::factory()->create([
        'book_id' => $book->id,
        'due_date' => now()->startOfDay()->subDays(3),
    ]);

    $this->actingAs($petugas)
        ->post(route('borrowings.return', $borrowing), ['status' => 'returned'])
        ->assertRedirect(route('borrowings.index'));

    expect($borrowing->refresh()->fine_amount)->toBe(6000)
        ->and($borrowing->late_days)->toBe(3);
});

it('menolak penghapusan anggota yang masih memiliki peminjaman', function () {
    $petugas = User::factory()->petugas()->create();
    $member = Member::factory()->create();
    Borrowing::factory()->returned()->create(['member_id' => $member->id]);

    $this->actingAs($petugas)
        ->delete(route('members.destroy', $member))
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('members', ['id' => $member->id]);
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
