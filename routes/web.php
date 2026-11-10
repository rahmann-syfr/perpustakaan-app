<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\MemberController; // Tambahkan MemberController jika ada

// Route untuk yang belum login (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    
    // Route untuk Katalog
    Route::get('/katalog', [BookController::class, 'katalog']);
    Route::get('/katalog/search-ajax', [BookController::class, 'searchAjax']);
});

// Route untuk yang sudah login (Auth)
Route::middleware('auth')->group(function () {
    
    // Bisa diakses SEMUA ROLE (Admin, Petugas, Anggota)
    Route::get('/', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/katalog', [BookController::class, 'katalog']);
    Route::get('/riwayat', [BorrowingController::class, 'history']);

    // HANYA bisa diakses oleh ADMIN (User Management & Akses Ubah/Hapus Kategori)
    Route::middleware('role:admin')->group(function () {
        // Route Manajemen User (Admin & Petugas)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/create', [UserController::class, 'create']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}/edit', [UserController::class, 'edit']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Khusus Admin: Tambah, Edit, Update, dan Hapus Kategori
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']); 
        Route::put('/categories/{id}', [CategoryController::class, 'update']);    
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });

    // Bisa diakses oleh ADMIN & PETUGAS (Operasional Perpustakaan)
    Route::middleware('role:petugas')->group(function () {
        // Petugas & Admin HANYA BISA MELIHAT daftar kategori
        Route::get('/categories', [CategoryController::class, 'index']);

        // Route Anggota (Member Management)
        Route::resource('members', MemberController::class);

        // Route Buku
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/create', [BookController::class, 'create']);
        Route::post('/books', [BookController::class, 'store']);
        Route::get('/books/{id}/edit', [BookController::class, 'edit']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);

        // Route Sirkulasi & Laporan
        Route::get('/borrowings', [BorrowingController::class, 'index']);
        Route::get('/borrowings/report-pdf', [BorrowingController::class, 'reportPdf']);
        Route::get('/borrowings/create', [BorrowingController::class, 'create']);
        Route::post('/borrowings', [BorrowingController::class, 'store']);
        Route::post('/borrowings/{id}/return', [BorrowingController::class, 'returnBook']);
    });
});