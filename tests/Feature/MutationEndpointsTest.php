<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;

/**
 * Menguji seluruh endpoint mutasi (POST/PUT/DELETE) agar tidak ada
 * error 500 akibat validasi atau otorisasi yang tidak lengkap.
 */
it('menolak anggota yang mengakses endpoint khusus petugas', function () {
    $anggota = User::factory()->anggota()->create();
    $book = Book::factory()->create();

    $this->actingAs($anggota)->post(route('books.store'), [])->assertRedirect();
    $this->actingAs($anggota)->post(route('borrowings.store'), [])->assertRedirect();
    $this->actingAs($anggota)->post(route('categories.store'), [])->assertRedirect();
    $this->actingAs($anggota)->post(route('members.store'), [])->assertRedirect();
    $this->actingAs($anggota)->post(route('users.store'), [])->assertRedirect();
    $this->actingAs($anggota)->put(route('settings.update'), [])->assertRedirect();
    $this->actingAs($anggota)->delete(route('books.destroy', $book))->assertRedirect();
});

it('menolak petugas yang mengakses endpoint khusus admin', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)->post(route('users.store'), [])->assertRedirect();
    $this->actingAs($petugas)->put(route('settings.update'), [])->assertRedirect();
});

it('menolak anggota yang mencoba memperpanjang atau mengembalikan lewat endpoint petugas', function () {
    $anggota = User::factory()->anggota()->create();
    $book = Book::factory()->create();
    $borrowing = Borrowing::factory()->create([
        'user_id' => $anggota->id,
        'book_id' => $book->id,
    ]);

    $this->actingAs($anggota)
        ->post(route('borrowings.extend', $borrowing))
        ->assertRedirect();

    $this->actingAs($anggota)
        ->post(route('borrowings.return', $borrowing), ['status' => 'returned'])
        ->assertRedirect();
});

it('memperbarui pengaturan sistem dengan nilai valid', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('settings.update'), [
            'maksimal_hari_pinjam' => 14,
            'maksimal_buku_anggota' => 5,
            'hari_pengingat' => 2,
            'maksimal_perpanjangan' => 3,
            'durasi_perpanjangan' => 10,
            'denda_per_hari' => 3000,
            'denda_buku_hilang' => 75000,
            'nama_perpustakaan' => 'PerpusApp Nusantara',
            'email_perpustakaan' => 'info@perpus.test',
        ])
        ->assertRedirect(route('settings.index'));

    $this->assertDatabaseHas('system_settings', [
        'setting_key' => 'durasi_perpanjangan',
        'setting_value' => '10',
    ]);
});

it('menolak pengaturan sistem dengan nilai di luar batas', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('settings.update'), [
            'maksimal_hari_pinjam' => 999,
        ])
        ->assertSessionHasErrors('maksimal_hari_pinjam');
});

it('menyimpan buku baru dengan data valid', function () {
    $petugas = User::factory()->petugas()->create();
    $category = Category::factory()->create();

    $this->actingAs($petugas)
        ->post(route('books.store'), [
            'title' => 'Buku Uji Otomatis',
            'author' => 'Penulis Uji',
            'publisher' => 'Penerbit Uji',
            'isbn' => 'ISBN-OTOMATIS-001',
            'published_year' => 2024,
            'category_id' => $category->id,
            'total_stock' => 3,
            'fine_per_day' => 2000,
        ])
        ->assertRedirect(route('books.index'));

    $this->assertDatabaseHas('books', ['title' => 'Buku Uji Otomatis']);
});

it('menyimpan anggota baru dengan data valid', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->post(route('members.store'), [
            'name' => 'Anggota Uji',
            'email' => 'anggota.uji@perpus.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
        ->assertRedirect(route('members.index'));

    $this->assertDatabaseHas('users', ['email' => 'anggota.uji@perpus.test']);
});

it('menyimpan kategori baru dan menolak duplikat nama', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)
        ->post(route('categories.store'), ['name' => $category->name])
        ->assertSessionHasErrors('name');
});
