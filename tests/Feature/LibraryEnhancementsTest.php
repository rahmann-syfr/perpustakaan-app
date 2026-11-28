<?php

use App\Models\Borrowing;
use App\Models\SystemSetting;
use App\Models\User;

it('mengekspor csv laporan sirkulasi', function () {
    $petugas = User::factory()->petugas()->create();
    Borrowing::factory()->create(['admin_id' => $petugas->id]);

    $response = $this->actingAs($petugas)
        ->get(route('borrowings.report-csv'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
    expect($response->headers->get('content-disposition'))->toContain('laporan-sirkulasi');
});

it('dashboard memuat aktivitas terbaru', function () {
    $petugas = User::factory()->petugas()->create();
    Borrowing::factory()->create(['admin_id' => $petugas->id]);

    $this->actingAs($petugas)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('recentActivities', fn ($items) => $items->count() === 1);
});

it('menghasilkan laporan pdf sirkulasi untuk 0 baris, 1 halaman, dan multi halaman tanpa denda', function () {
    SystemSetting::set('nama_perpustakaan', 'Perpustakaan Uji');
    $petugas = User::factory()->petugas()->create(['name' => 'Petugas Cetak']);

    // 1. 0 baris
    $emptyResponse = $this->actingAs($petugas)
        ->get(route('borrowings.report'));
    $emptyResponse->assertOk();
    expect($emptyResponse->headers->get('content-type'))->toContain('application/pdf')
        ->and($emptyResponse->getContent())->toStartWith('%PDF');

    // 2. 1 halaman (3 baris)
    Borrowing::factory()->count(3)->create(['admin_id' => $petugas->id]);
    $singlePage = $this->actingAs($petugas)
        ->get(route('borrowings.report', ['filter' => 'bulan_ini']));
    $singlePage->assertOk();
    expect($singlePage->getContent())->toStartWith('%PDF');

    // 3. Multi halaman (30 baris)
    Borrowing::factory()->count(27)->create(['admin_id' => $petugas->id]);
    $multiPage = $this->actingAs($petugas)
        ->get(route('borrowings.report'));
    $multiPage->assertOk();
    expect($multiPage->getContent())->toStartWith('%PDF');

    // Verifikasi isi view HTML sebelum dirender DomPDF
    $html = view('borrowings.report', [
        'borrowings' => Borrowing::with(['member', 'admin', 'book'])->get(),
        'filters' => ['filter' => 'bulan_ini'],
    ])->render();

    expect($html)
        ->toContain('Perpustakaan Uji')
        ->toContain('Total Transaksi')
        ->not->toContain('Denda')
        ->not->toContain('SHA-256');
});
