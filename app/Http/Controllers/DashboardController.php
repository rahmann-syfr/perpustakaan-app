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
        $totalBuku = Book::sum('total_stock'); 
        $bukuDipinjam = Borrowing::where('status', 'borrowed')->count(); 
        $totalAnggota = User::where('role', 'anggota')->count(); 
        $totalDenda = Borrowing::sum('fine_amount'); 

        $pinjamanSaya = 0;
        if (Auth::user()->role == 'anggota') {
            $pinjamanSaya = Borrowing::where('user_id', Auth::id())
                                     ->where('status', 'borrowed')
                                     ->count();
        }

        return view('dashboard', compact('totalBuku', 'bukuDipinjam', 'totalAnggota', 'totalDenda', 'pinjamanSaya'));
    }
}