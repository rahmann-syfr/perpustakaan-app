<?php

use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;

it('merender dashboard untuk anggota tanpa membocorkan daftar anggota lain', function () {
    $anggota = User::factory()->anggota()->create();
    $anggotaLain = User::factory()->anggota()->create(['name' => 'Rahasia Member']);

    Borrowing::factory()->create(['user_id' => $anggota->id]);
    Borrowing::factory()->create(['user_id' => $anggotaLain->id]);

    $this->actingAs($anggota)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Rahasia Member');
});

it('merender dashboard untuk petugas tanpa kartu statistik anggota', function () {
    $petugas = User::factory()->petugas()->create();
    User::factory()->anggota()->create(['name' => 'Budi Santoso']);

    $this->actingAs($petugas)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Total Anggota', false)
        ->assertDontSee('Budi Santoso', false);
});

it('merender halaman data buku beserta hasil pencarian', function () {
    $petugas = User::factory()->petugas()->create();
    Book::factory()->create(['title' => 'Belajar Alpine.js']);

    $this->actingAs($petugas)
        ->get(route('books.index', ['search' => 'Alpine']))
        ->assertOk()
        ->assertSee('Belajar Alpine.js');
});

it('merender form edit kategori', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Kategori Edit']);

    $this->actingAs($admin)
        ->get(route('categories.edit', $category))
        ->assertOk()
        ->assertSee('Kategori Edit');
});

it('menolak nama kategori yang duplikat', function () {
    $admin = User::factory()->admin()->create();
    Category::factory()->create(['name' => 'Sastra']);

    $this->actingAs($admin)
        ->post(route('categories.store'), ['name' => 'Sastra'])
        ->assertSessionHasErrors('name');
});

it('merender form tambah dan edit user', function () {
    $superadmin = User::factory()->superadmin()->create();
    $target = User::factory()->anggota()->create();

    $this->actingAs($superadmin)
        ->get(route('users.create'))
        ->assertOk();

    $this->actingAs($superadmin)
        ->get(route('users.edit', $target))
        ->assertOk();
});

it('merender daftar buku dan daftar kategori', function () {
    $petugas = User::factory()->petugas()->create();
    Book::factory()->create(['title' => 'Buku Untuk Indeks']);
    Category::factory()->create(['name' => 'Kategori Indeks']);

    $this->actingAs($petugas)
        ->get(route('books.index'))
        ->assertOk()
        ->assertSee('Buku Untuk Indeks');

    $this->actingAs($petugas)
        ->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Kategori Indeks');
});

it('merender form tambah dan edit buku', function () {
    $petugas = User::factory()->petugas()->create();
    $book = Book::factory()->create();

    $this->actingAs($petugas)
        ->get(route('books.create'))
        ->assertOk();

    $this->actingAs($petugas)
        ->get(route('books.edit', $book))
        ->assertOk();
});

it('merender daftar sirkulasi beserta statistiknya', function () {
    $petugas = User::factory()->petugas()->create();
    Borrowing::factory()->create(['admin_id' => $petugas->id]);
    Borrowing::factory()->returned()->create(['admin_id' => $petugas->id]);

    $this->actingAs($petugas)
        ->get(route('borrowings.index'))
        ->assertOk();
});

it('merender form peminjaman dengan durasi dari pengaturan sistem', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->get(route('borrowings.create'))
        ->assertOk()
        ->assertSee('Durasi Peminjaman');
});

it('menampilkan status terlambat pada daftar sirkulasi', function () {
    $petugas = User::factory()->petugas()->create();
    Borrowing::factory()->overdue(5)->create(['admin_id' => $petugas->id]);

    $this->actingAs($petugas)
        ->get(route('borrowings.index'))
        ->assertOk()
        ->assertSee('Terlambat');
});

it('menghasilkan pdf laporan yang mengikuti filter aktif', function () {
    $petugas = User::factory()->petugas()->create();
    Borrowing::factory()->create(['admin_id' => $petugas->id]);

    $response = $this->actingAs($petugas)
        ->get(route('borrowings.report', ['filter' => 'bulan_ini']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('menghitung transaksi yang lewat tenggat pada statistik sirkulasi', function () {
    $petugas = User::factory()->petugas()->create();

    // Satu transaksi lewat tenggat dan masih dipinjam.
    Borrowing::factory()->create([
        'admin_id' => $petugas->id,
        'due_date' => now()->startOfDay()->subDays(2),
        'return_date' => null,
    ]);

    // Satu transaksi tepat waktu.
    Borrowing::factory()->create([
        'admin_id' => $petugas->id,
        'due_date' => now()->startOfDay()->addDays(3),
        'return_date' => null,
    ]);

    $this->actingAs($petugas)
        ->get(route('borrowings.index'))
        ->assertOk()
        ->assertViewHas('stats', fn ($stats) => $stats['terlambat'] === 1 && $stats['dipinjam'] === 2);
});

it('menampilkan label status enum dengan benar', function () {
    expect(BorrowingStatus::Borrowed->label())->toBe('Dipinjam')
        ->and(BorrowingStatus::Returned->label())->toBe('Dikembalikan')
        ->and(BorrowingStatus::Overdue->label())->toBe('Terlambat')
        ->and(BorrowingStatus::Lost->label())->toBe('Hilang');
});
