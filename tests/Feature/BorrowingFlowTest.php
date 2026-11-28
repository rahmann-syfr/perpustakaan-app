<?php

use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\SystemSetting;
use App\Models\User;

it('menolak anggota membuka halaman sirkulasi', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->get(route('borrowings.index'))
        ->assertRedirect(route('dashboard'));
});

it('menolak petugas membuka manajemen user', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->get(route('users.index'))
        ->assertRedirect(route('dashboard'));
});

it('menolak anggota membuat buku', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->get(route('books.create'))
        ->assertRedirect(route('dashboard'));
});

it('memberikan superadmin akses penuh kelola user', function () {
    $superadmin = User::factory()->superadmin()->create();

    $this->actingAs($superadmin)
        ->get(route('users.index'))
        ->assertOk();
});

it('menurunkan stok tersedia saat peminjaman', function () {
    $petugas = User::factory()->petugas()->create();
    $member = Member::factory()->create();
    $book = Book::factory()->create(['total_stock' => 3, 'available_stock' => 3]);

    $this->actingAs($petugas)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'duration_days' => 7,
        ])
        ->assertRedirect(route('borrowings.index'));

    expect($book->fresh()->available_stock)->toBe(2);

    $this->assertDatabaseHas('borrowings', [
        'book_id' => $book->id,
        'member_id' => $member->id,
        'status' => BorrowingStatus::Borrowed->value,
    ]);
});

it('menolak peminjaman saat stok buku kosong', function () {
    $petugas = User::factory()->petugas()->create();
    $member = Member::factory()->create();
    $book = Book::factory()->create(['total_stock' => 1, 'available_stock' => 0]);

    $this->actingAs($petugas)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'duration_days' => 7,
        ])
        ->assertSessionHasErrors('book_id');

    expect(Borrowing::count())->toBe(0);
});

it('menolak durasi peminjaman melebihi batas sistem', function () {
    $petugas = User::factory()->petugas()->create();
    $member = Member::factory()->create();
    $book = Book::factory()->create();

    SystemSetting::query()->updateOrCreate(
        ['setting_key' => 'maksimal_hari_pinjam'],
        ['setting_value' => '14']
    );
    SystemSetting::flush();

    $this->actingAs($petugas)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'duration_days' => 15,
        ])
        ->assertSessionHasErrors('duration_days');

    $this->actingAs($petugas)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'duration_days' => 14,
        ])
        ->assertSessionHasNoErrors();
});

it('menerima durasi peminjaman sesuai batas konfigurasi', function () {
    $petugas = User::factory()->petugas()->create();
    $member = Member::factory()->create();
    $book = Book::factory()->create(['total_stock' => 2, 'available_stock' => 2]);

    SystemSetting::query()->updateOrCreate(
        ['setting_key' => 'maksimal_hari_pinjam'],
        ['setting_value' => '30']
    );
    SystemSetting::flush();

    $this->actingAs($petugas)
        ->post(route('borrowings.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'duration_days' => 30,
        ])
        ->assertSessionHasNoErrors();

    expect($book->fresh()->available_stock)->toBe(1);
});

it('menolak peminjaman jika member_id tidak terdaftar di tabel members', function () {
    $petugas = User::factory()->petugas()->create();
    $book = Book::factory()->create();

    $this->actingAs($petugas)
        ->post(route('borrowings.store'), [
            'member_id' => 999999,
            'book_id' => $book->id,
            'duration_days' => 7,
        ])
        ->assertSessionHasErrors('member_id');
});

it('tidak menghasilkan denda saat pengembalian tepat waktu', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->create([
        'admin_id' => $petugas->id,
        'due_date' => now()->startOfDay(),
    ]);

    $this->actingAs($petugas)
        ->post(route('borrowings.return', $borrowing), ['status' => 'returned'])
        ->assertRedirect(route('borrowings.index'));

    $borrowing->refresh();

    expect($borrowing->status)->toBe(BorrowingStatus::Returned)
        ->and($borrowing->fine_amount)->toBe(0)
        ->and($borrowing->late_days)->toBe(0);
});

it('menghitung denda keterlambatan per hari', function () {
    $petugas = User::factory()->petugas()->create();
    $book = Book::factory()->create(['fine_per_day' => 1000]);
    $borrowing = Borrowing::factory()->create([
        'admin_id' => $petugas->id,
        'book_id' => $book->id,
        'due_date' => now()->startOfDay()->subDays(3),
    ]);

    $this->actingAs($petugas)
        ->post(route('borrowings.return', $borrowing), ['status' => 'returned']);

    $borrowing->refresh();

    expect($borrowing->late_days)->toBe(3)
        ->and($borrowing->fine_amount)->toBe(3000);
});

it('tidak mengembalikan stok untuk buku yang hilang', function () {
    $petugas = User::factory()->petugas()->create();
    $book = Book::factory()->create(['total_stock' => 2, 'available_stock' => 1]);
    $borrowing = Borrowing::factory()->create([
        'admin_id' => $petugas->id,
        'book_id' => $book->id,
    ]);

    $this->actingAs($petugas)
        ->post(route('borrowings.return', $borrowing), [
            'status' => 'lost',
            'lost_fine_amount' => 50000,
        ]);

    $borrowing->refresh();

    expect($borrowing->status)->toBe(BorrowingStatus::Lost)
        ->and($borrowing->fine_amount)->toBe(50000)
        ->and($book->fresh()->available_stock)->toBe(1);
});

it('menolak pengembalian transaksi yang sudah selesai', function () {
    $petugas = User::factory()->petugas()->create();
    $borrowing = Borrowing::factory()->returned()->create(['admin_id' => $petugas->id]);

    $this->actingAs($petugas)
        ->post(route('borrowings.return', $borrowing), ['status' => 'returned'])
        ->assertSessionHasErrors('status');
});

it('dashboard anggota tidak menampilkan kartu total anggota', function () {
    $anggota = User::factory()->anggota()->create();

    $this->actingAs($anggota)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Total Anggota', false);
});

it('dashboard admin menampilkan kartu total anggota', function () {
    $admin = User::factory()->admin()->create();
    Member::factory()->count(2)->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Total Anggota', false);
});
