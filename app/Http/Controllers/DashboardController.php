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
        // Menghitung jumlah JUDUL BUKU (bukan total stok fisik)
        $totalBuku = Book::count(); 
        
        // Mengambil data buku untuk ditampilkan di dalam popup modal
        $booksData = Book::orderBy('title', 'asc')->get();

        $bukuDipinjam = Borrowing::where('status', 'borrowed')->count(); 
        $totalAnggota = User::where('role', 'anggota')->count(); 
        $totalDenda = Borrowing::sum('fine_amount'); 

        $pinjamanSaya = 0;
        if (Auth::user()->role == 'anggota') {
            $pinjamanSaya = Borrowing::where('user_id', Auth::id())
                                     ->where('status', 'borrowed')
                                     ->count();
        }

        // Variabel $booksData dikirim ke view melalui compact()
        return view('dashboard', compact('totalBuku', 'booksData', 'bukuDipinjam', 'totalAnggota', 'totalDenda', 'pinjamanSaya'));
    }
}