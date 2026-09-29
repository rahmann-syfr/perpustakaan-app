<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;

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
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('/katalog', [App\Http\Controllers\BookController::class, 'katalog']);

    // HANYA bisa diakses oleh ADMIN & SUPERADMIN
    Route::middleware('role:admin,superadmin')->group(function () {
        
        // Route Kategori
        Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index']);
        Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store']);
        Route::delete('/categories/{id}', [App\Http\Controllers\CategoryController::class, 'destroy']);

        // Route Buku
        Route::get('/books', [App\Http\Controllers\BookController::class, 'index']);
        Route::get('/books/create', [App\Http\Controllers\BookController::class, 'create']);
        Route::post('/books', [App\Http\Controllers\BookController::class, 'store']);
        Route::delete('/books/{id}', [App\Http\Controllers\BookController::class, 'destroy']);

        // Route Sirkulasi
        Route::get('/borrowings', [App\Http\Controllers\BorrowingController::class, 'index']);
        Route::get('/borrowings/create', [App\Http\Controllers\BorrowingController::class, 'create']);
        Route::post('/borrowings', [App\Http\Controllers\BorrowingController::class, 'store']);
        Route::post('/borrowings/{id}/return', [App\Http\Controllers\BorrowingController::class, 'returnBook']);
        
    });
});