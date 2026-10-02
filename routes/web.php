<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController; // Import controller baru

// Route untuk yang belum login (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    
    // Route untuk Katalog (Bisa diakses semua role)
    Route::get('/katalog', [BookController::class, 'katalog']);
});

// Route untuk yang sudah login (Auth)
Route::middleware('auth')->group(function () {
    
    // Bisa diakses SEMUA ROLE (Superadmin, Admin, Anggota)
    Route::get('/', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/katalog', [BookController::class, 'katalog']);
    Route::get('/riwayat', [BorrowingController::class, 'history']);

    // HANYA bisa diakses oleh ADMIN & SUPERADMIN
    Route::middleware('role:admin,superadmin')->group(function () {
        
        // Route Kategori
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Route Buku
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/create', [BookController::class, 'create']);
        Route::post('/books', [BookController::class, 'store']);
        Route::get('/books/{id}/edit', [BookController::class, 'edit']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);

        // Route Manajemen Anggota
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/create', [UserController::class, 'create']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}/edit', [UserController::class, 'edit']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Route Sirkulasi
        Route::get('/borrowings', [BorrowingController::class, 'index']);
        Route::get('/borrowings/create', [BorrowingController::class, 'create']);
        Route::post('/borrowings', [BorrowingController::class, 'store']);
        Route::post('/borrowings/{id}/return', [BorrowingController::class, 'returnBook']);
        
    });
});