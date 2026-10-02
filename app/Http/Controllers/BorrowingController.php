<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    // Menampilkan riwayat transaksi (Fungsi untuk Admin)
    public function index()
    {
        // Memuat relasi user (peminjam), admin (petugas), dan book (buku)
        $borrowings = Borrowing::with(['user', 'admin', 'book'])->latest()->get();
        return view('borrowings.index', compact('borrowings'));
    }

    // Menampilkan form peminjaman baru
    public function create()
    {
        // Hanya ambil anggota untuk opsi peminjam
        $members = User::where('role', 'anggota')->get();
        // Hanya ambil buku yang stoknya masih ada
        $books = Book::where('available_stock', '>', 0)->get();
        
        return view('borrowings.create', compact('members', 'books'));
    }

    // Memproses peminjaman buku
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        // Validasi ekstra untuk memastikan stok tidak minus
        if ($book->available_stock < 1) {
            return back()->withErrors(['book_id' => 'Stok buku ini sedang kosong.']);
        }

        // Simpan transaksi dengan batas waktu 7 hari
        Borrowing::create([
            'user_id' => $request->user_id,
            'admin_id' => Auth::id(), // Petugas yang sedang login
            'book_id' => $request->book_id,
            'borrow_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(7),
            'status' => 'borrowed',
        ]);

        // Kurangi stok buku yang tersedia
        $book->decrement('available_stock');

        return redirect('/borrowings')->with('success', 'Buku berhasil dipinjamkan!');
    }

    // Memproses pengembalian buku dan denda
    public function returnBook($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->status != 'borrowed') {
            return back()->withErrors(['error' => 'Buku ini sudah dikembalikan atau statusnya tidak valid.']);
        }

        $now = Carbon::now();
        $dueDate = Carbon::parse($borrowing->due_date);
        
        $fineAmount = 0;
        $status = 'returned';

        // Hitung denda jika tanggal sekarang melebihi batas waktu (due_date)
        if ($now->greaterThan($dueDate)) {
            $daysLate = $dueDate->diffInDays($now);
            $finePerDay = 2000; // Setup denda Rp 2.000 per hari
            
            $fineAmount = $daysLate * $finePerDay; // Perbaikan syntax error di sini
            $status = 'overdue'; 
        }

        // Update transaksi
        $borrowing->update([
            'return_date' => $now,
            'status' => $status,
            'fine_amount' => $fineAmount,
        ]);

        // Kembalikan stok buku
        $borrowing->book->increment('available_stock');

        $message = $fineAmount > 0 
            ? "Buku dikembalikan. Terdapat denda keterlambatan sebesar Rp" . number_format($fineAmount, 0, ',', '.') 
            : "Buku berhasil dikembalikan tepat waktu.";

        return redirect('/borrowings')->with('success', $message);
    }

    // Menampilkan riwayat peminjaman khusus untuk mahasiswa yang sedang login
    public function history()
    {
        $riwayat = Borrowing::with(['book', 'admin'])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();

        return view('riwayat', compact('riwayat'));
    }
}