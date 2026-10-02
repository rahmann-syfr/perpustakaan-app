<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Jika yang login adalah anggota, arahkan langsung ke halaman katalog
        if (Auth::user()->role == 'anggota') {
            return redirect('/katalog');
        }

        // Hitung statistik untuk Dashboard Admin
        $totalBuku = Book::sum('total_stock'); // Menghitung seluruh fisik buku
        $bukuDipinjam = Borrowing::where('status', 'borrowed')->count(); // Transaksi aktif
        $totalAnggota = User::where('role', 'anggota')->count(); // Jumlah mahasiswa/anggota
        $totalDenda = Borrowing::sum('fine_amount'); // Total denda yang terkumpul

        return view('dashboard', compact('totalBuku', 'bukuDipinjam', 'totalAnggota', 'totalDenda'));
    }
}