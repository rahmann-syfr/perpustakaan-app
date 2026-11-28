<?php

use App\Enums\Role;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;

it('mengarahkan tamu ke halaman login untuk seluruh route terproteksi', function (string $routeName) {
    $this->get(route($routeName))
        ->assertRedirect(route('login'));
})->with([
    'dashboard' => ['dashboard'],
    'profil' => ['profile.show'],
    'kategori' => ['categories.index'],
    'buku' => ['books.index'],
    'anggota' => ['members.index'],
    'sirkulasi' => ['borrowings.index'],
    'user' => ['users.index'],
    'pengaturan' => ['settings.index'],
]);

it('petugas dapat melihat daftar kategori tetapi ditolak saat tambah edit atau hapus kategori', function () {
    $petugas = User::factory()->petugas()->create();
    $category = Category::factory()->create(['name' => 'Fiksi']);

    $this->actingAs($petugas)
        ->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Fiksi');

    $this->actingAs($petugas)
        ->post(route('categories.store'), ['name' => 'Baru'])
        ->assertRedirect(route('dashboard'));

    $this->actingAs($petugas)
        ->get(route('categories.edit', $category))
        ->assertRedirect(route('dashboard'));

    $this->actingAs($petugas)
        ->put(route('categories.update', $category), ['name' => 'Diubah'])
        ->assertRedirect(route('dashboard'));

    $this->actingAs($petugas)
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Fiksi']);
});

it('petugas tidak dapat mengakses manajemen user dan pengaturan sistem', function () {
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($petugas)->get(route('users.index'))->assertRedirect(route('dashboard'));
    $this->actingAs($petugas)->get(route('users.create'))->assertRedirect(route('dashboard'));
    $this->actingAs($petugas)->post(route('users.store'), [])->assertRedirect(route('dashboard'));
    $this->actingAs($petugas)->get(route('settings.index'))->assertRedirect(route('dashboard'));
    $this->actingAs($petugas)->put(route('settings.update'), [])->assertRedirect(route('dashboard'));
});

it('admin dapat mengakses manajemen user, kategori, dan pengaturan sistem', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)->get(route('users.index'))->assertOk();
    $this->actingAs($admin)->get(route('settings.index'))->assertOk();
    $this->actingAs($admin)->get(route('categories.index'))->assertOk();
    $this->actingAs($admin)->get(route('categories.edit', $category))->assertOk();
});

it('admin tidak dapat menghapus akunnya sendiri atau menurunkan role admin terakhir', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('users.destroy', $admin))
        ->assertForbidden();

    $this->actingAs($admin)
        ->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => Role::Petugas->value,
        ])
        ->assertSessionHasErrors('role');

    expect($admin->fresh()->role)->toBe(Role::Admin);
});

it('form anggota menolak data tidak valid dan tidak membuat akun user', function () {
    $petugas = User::factory()->petugas()->create();
    $userCountBefore = User::query()->count();

    $this->actingAs($petugas)
        ->post(route('members.store'), [
            'name' => '',
            'email' => 'bukan-email',
            'phone' => 'abc123',
            'address' => '',
        ])
        ->assertSessionHasErrors(['name', 'email', 'phone', 'address']);

    $this->actingAs($petugas)
        ->post(route('members.store'), [
            'name' => 'Dewi Lestari',
            'email' => 'dewi@member.test',
            'phone' => '0812-3456-7890',
            'address' => 'Jl. Sudirman No. 12',
        ])
        ->assertRedirect(route('members.index'));

    $this->assertDatabaseHas('members', [
        'email' => 'dewi@member.test',
        'phone' => '0812-3456-7890',
    ]);
    expect(User::query()->count())->toBe($userCountBefore);
});

it('menolak penghapusan anggota yang masih memiliki riwayat peminjaman', function () {
    $petugas = User::factory()->petugas()->create();
    $member = Member::factory()->create();
    Borrowing::factory()->returned()->create(['member_id' => $member->id]);

    $this->actingAs($petugas)
        ->delete(route('members.destroy', $member))
        ->assertRedirect(route('members.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('members', ['id' => $member->id]);
});
