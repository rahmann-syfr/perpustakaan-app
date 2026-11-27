<?php

use App\Enums\BorrowingStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\FinePayment;
use App\Models\User;

/**
 * Smoke test komprehensif: merender setiap halaman untuk setiap role
 * dengan kondisi data berbeda (kosong, terisi, ekstrem).
 */
it('merender seluruh halaman utama untuk semua role tanpa error', function () {
    $halamanStaff = [
        'dashboard', 'profile.show', 'fines.index',
        'borrowings.index', 'books.index', 'categories.index', 'members.index',
        'analytics.index', 'borrowings.create', 'books.create', 'members.create',
    ];

    // Manajemen pengguna dan pengaturan sistem hanya untuk admin/superadmin.
    $halamanAdmin = ['users.index', 'users.create', 'settings.index'];

    $petugas = User::factory()->petugas()->create();
    $admin = User::factory()->admin()->create();

    foreach ($halamanStaff as $routeName) {
        $this->actingAs($petugas)->get(route($routeName))->assertOk();
    }

    foreach ($halamanAdmin as $routeName) {
        $this->actingAs($admin)->get(route($routeName))->assertOk();
    }
});

it('merender halaman staf dengan data lengkap', function () {
    $petugas = User::factory()->petugas()->create();
    $anggota = User::factory()->anggota()->create();
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id]);

    $borrowing = Borrowing::factory()->create([
        'user_id' => $anggota->id,
        'book_id' => $book->id,
        'status' => BorrowingStatus::Overdue,
        'fine_amount' => 5000,
    ]);

    FinePayment::factory()->create(['borrowing_id' => $borrowing->id, 'user_id' => $anggota->id]);

    foreach (['dashboard', 'fines.index', 'borrowings.index',
        'members.index', 'books.index', 'categories.index', 'analytics.index'] as $routeName) {
        $this->actingAs($petugas)->get(route($routeName))->assertOk();
    }

    // Manajemen pengguna dan pengaturan sistem hanya untuk admin/superadmin.
    foreach (['users.index', 'settings.index'] as $routeName) {
        $this->actingAs(User::factory()->admin()->create())->get(route($routeName))->assertOk();
    }
});

it('merender halaman detail anggota dan buku', function () {
    $petugas = User::factory()->petugas()->create();
    $anggota = User::factory()->anggota()->create();
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id]);

    $this->actingAs($petugas)->get(route('members.show', $anggota->id))->assertOk();
    $this->actingAs($petugas)->get(route('books.show', $book->id))->assertOk();
    $this->actingAs($petugas)->get(route('members.edit', $anggota->id))->assertOk();
    $this->actingAs($petugas)->get(route('books.edit', $book->id))->assertOk();
});

it('merender halaman profil pengguna', function () {
    $petugas = User::factory()->petugas()->create([
        'name' => 'Petugas Perpus',
        'email' => 'petugas.profil@perpus.test',
    ]);

    $this->actingAs($petugas)->get(route('profile.show'))->assertOk()
        ->assertSee('Petugas Perpus')
        ->assertSee('petugas.profil@perpus.test');
});

it('merender halaman denda saat tabel kosong dan terisi', function () {
    $petugas = User::factory()->petugas()->create();
    $anggota = User::factory()->anggota()->create();
    $book = Book::factory()->create();

    $this->actingAs($petugas)->get(route('fines.index'))->assertOk();

    $borrowing = Borrowing::factory()->create([
        'user_id' => $anggota->id,
        'book_id' => $book->id,
        'fine_amount' => 10000,
    ]);

    // Pembayaran parsial sehingga masih ada sisa denda yang harus dibayar.
    FinePayment::factory()->create([
        'borrowing_id' => $borrowing->id,
        'user_id' => $anggota->id,
        'amount' => 4000,
    ]);

    $this->actingAs($petugas)->get(route('fines.index'))->assertOk();
    $this->actingAs($petugas)->get(route('fines.create', $borrowing))->assertOk();
});

it('mengunduh laporan PDF sirkulasi tanpa error', function () {
    $petugas = User::factory()->petugas()->create();
    $anggota = User::factory()->anggota()->create();
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id]);

    Borrowing::factory()->create([
        'user_id' => $anggota->id,
        'book_id' => $book->id,
    ]);

    $response = $this->actingAs($petugas)->get(route('borrowings.report'));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

it('menyusun laporan PDF saat tabel borrowings kosong', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)->get(route('borrowings.report'))->assertOk();
});

it('mengekspor laporan CSV sirkulasi tanpa error', function () {
    $petugas = User::factory()->petugas()->create();
    $anggota = User::factory()->anggota()->create();
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id]);

    Borrowing::factory()->create([
        'user_id' => $anggota->id,
        'book_id' => $book->id,
    ]);

    $response = $this->actingAs($petugas)->get(route('borrowings.report-csv'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
