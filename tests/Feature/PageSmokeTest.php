<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;

/**
 * Smoke test: merender SETIAP halaman GET untuk SETIAP role.
 *
 * Tujuannya menangkap error runtime (undef variable, enum mismatch, null
 * property) yang tidak tertangkap oleh test fungsional biasa.
 */
function seedPerpusData(): array
{
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id]);
    $bookLain = Book::factory()->create(['category_id' => $category->id]);
    $anggota = Member::factory()->create();
    $targetUser = User::factory()->petugas()->create();

    Borrowing::factory()->create(['member_id' => $anggota->id, 'book_id' => $book->id]);
    Borrowing::factory()->overdue(5)->create([
        'member_id' => $anggota->id,
        'book_id' => $book->id,
        'admin_id' => null,
    ]);
    Borrowing::factory()->returned()->create([
        'member_id' => $anggota->id,
        'book_id' => $book->id,
        'admin_id' => null,
    ]);

    return [
        'category' => $category,
        'book' => $book,
        'bookLain' => $bookLain,
        'anggota' => $anggota,
        'targetUser' => $targetUser,
    ];
}

/**
 * Daftar halaman GET: nama => [nama route, kunci parameter model].
 *
 * Kunci parameter diisi saat test berjalan (butuh koneksi database),
 * sehingga dataset tetap bisa didefinisikan secara statis.
 *
 * @return array<string, array{0: string, 1: array<string, string>}>
 */
function daftarHalamanGet(): array
{
    return [
        'dashboard' => ['dashboard', []],
        'profil' => ['profile.show', []],
        'buku show' => ['books.show', ['book']],
        'pengaturan' => ['settings.index', []],
        'anggota' => ['members.index', []],
        'anggota cari' => ['members.index', []],
        'anggota status' => ['members.index', []],
        'anggota detail' => ['members.show', ['member']],
        'anggota form' => ['members.create', []],
        'anggota edit' => ['members.edit', ['member']],
        'buku data' => ['books.index', []],
        'buku form' => ['books.create', []],
        'buku edit' => ['books.edit', ['book']],
        'kategori' => ['categories.index', []],
        'kategori edit' => ['categories.edit', ['category']],
        'sirkulasi' => ['borrowings.index', []],
        'sirkulasi form' => ['borrowings.create', []],
        'pengguna' => ['users.index', []],
        'pengguna form' => ['users.create', []],
        'pengguna edit' => ['users.edit', ['user']],
    ];
}

/**
 * Membangun parameter route dari kunci model.
 *
 * @param  array<string, string>  $kunci
 * @return array<string, mixed>
 */
function parameterRoute(array $kunci, array $data, array $query = []): array
{
    $parameter = [];

    foreach ($kunci as $key) {
        $parameter[$key] = match ($key) {
            'member' => $data['anggota'],
            'user' => $data['targetUser'],
            default => $data[$key],
        };
    }

    return array_merge($parameter, $query);
}

function datasetHalaman(): array
{
    $dataset = [];

    foreach (daftarHalamanGet() as $nama => [$route, $kunci]) {
        $dataset[$nama] = [$route, $kunci];
    }

    return $dataset;
}

it('merender daftar anggota dengan filter terlambat tanpa error', function () {
    $petugas = User::factory()->petugas()->create();
    $terlambat = Member::factory()->create(['name' => 'Anggota Terlambat']);
    $normal = Member::factory()->create(['name' => 'Anggota Normal']);

    Borrowing::factory()->overdue(5)->create([
        'member_id' => $terlambat->id,
        'admin_id' => null,
    ]);
    Borrowing::factory()->create([
        'member_id' => $normal->id,
        'admin_id' => null,
        'due_date' => now()->startOfDay()->addDays(5),
    ]);

    $response = $this->actingAs($petugas)
        ->get(route('members.index', ['status' => 'terlambat']));

    $response->assertOk()
        ->assertSee('Anggota Terlambat')
        ->assertDontSee('Anggota Normal');
});

it('merender daftar anggota dengan filter tidak meminjam tanpa error', function () {
    $petugas = User::factory()->petugas()->create();
    $aktif = Member::factory()->create(['name' => 'Anggota Aktif']);
    $nonaktif = Member::factory()->create(['name' => 'Anggota Nonaktif']);

    Borrowing::factory()->create(['member_id' => $aktif->id, 'admin_id' => null]);

    $response = $this->actingAs($petugas)
        ->get(route('members.index', ['status' => 'nonaktif']));

    $response->assertOk()
        ->assertSee('Anggota Nonaktif')
        ->assertDontSee('Anggota Aktif');
});

it('merender daftar anggota dengan pencarian nama dan email', function () {
    $petugas = User::factory()->petugas()->create();
    Member::factory()->create([
        'name' => 'Cahyani Hamzah',
        'email' => 'cahyani@perpus.test',
    ]);
    Member::factory()->create([
        'name' => 'Budi Santoso',
        'email' => 'budi@perpus.test',
    ]);

    $this->actingAs($petugas)
        ->get(route('members.index', ['search' => 'cahyani']))
        ->assertOk()
        ->assertSee('Cahyani Hamzah')
        ->assertDontSee('Budi Santoso');
});

it('merender pengaturan dengan nilai default saat belum pernah disimpan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('settings.index'))
        ->assertOk()
        ->assertSee('Maksimal Durasi Pinjam');
});

it('merender profil pengguna tanpa error', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)
        ->get(route('profile.show'))
        ->assertOk();
});

it('menyediakan kolom pencarian dan empty state pada halaman data', function (string $role, string $route) {
    $petugas = User::factory()->{$role}()->create();
    Member::factory()->create(['name' => 'Anggota searchable']);

    $response = $this->actingAs($petugas)->get(route($route));

    $response->assertOk()
        ->assertSee('id="tableSearch"', false)
        ->assertSee('id="tableEmpty"', false);
})->with([
    'anggota' => ['petugas', 'members.index'],
    'buku' => ['petugas', 'books.index'],
    'pengguna' => ['admin', 'users.index'],
    'kategori' => ['petugas', 'categories.index'],
    'sirkulasi' => ['petugas', 'borrowings.index'],
]);

it('merender seluruh halaman untuk setiap role tanpa error', function (string $role, array $halaman) {
    $data = seedPerpusData();
    $pengguna = User::factory()->{$role}()->create();

    foreach ($halaman as $nama => [$route, $kunci, $query]) {
        $parameter = parameterRoute($kunci, $data, $query);

        $response = $this->actingAs($pengguna)->get(route($route, $parameter));

        $response->assertOk("Halaman [{$nama}] gagal dirender untuk role {$role}.");
    }
})->with([
    'petugas' => ['petugas', [
        'dashboard' => ['dashboard', [], []],
        'profil' => ['profile.show', [], []],
        'buku show' => ['books.show', ['book'], []],
        'anggota' => ['members.index', [], []],
        'anggota cari' => ['members.index', [], ['search' => 'a']],
        'anggota status' => ['members.index', [], ['status' => 'terlambat']],
        'anggota detail' => ['members.show', ['member'], []],
        'anggota form' => ['members.create', [], []],
        'anggota edit' => ['members.edit', ['member'], []],
        'buku data' => ['books.index', [], []],
        'buku form' => ['books.create', [], []],
        'buku edit' => ['books.edit', ['book'], []],
        'kategori' => ['categories.index', [], []],
        'sirkulasi' => ['borrowings.index', [], []],
        'sirkulasi form' => ['borrowings.create', [], []],
    ]],
    'admin' => ['admin', [
        'dashboard' => ['dashboard', [], []],
        'pengaturan' => ['settings.index', [], []],
        'anggota' => ['members.index', [], []],
        'pengguna' => ['users.index', [], []],
        'pengguna form' => ['users.create', [], []],
        'pengguna edit' => ['users.edit', ['user'], []],
        'sirkulasi' => ['borrowings.index', [], []],
        'buku data' => ['books.index', [], []],
        'kategori' => ['categories.index', [], []],
        'kategori edit' => ['categories.edit', ['category'], []],
    ]],
]);
