<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil rekap data untuk ditampilkan di Dashboard
        $totalBuku = Book::sum('total_stock'); // Menjumlahkan seluruh stok buku
        $totalAnggota = User::where('role', 'anggota')->count(); // Menghitung total mahasiswa
        $bukuDipinjam = Borrowing::where('status', 'borrowed')->count(); // Menghitung buku yang masih diluar

        return view('dashboard', compact('totalBuku', 'totalAnggota', 'bukuDipinjam'));
    }
}