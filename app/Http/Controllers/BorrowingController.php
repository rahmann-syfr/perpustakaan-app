<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BorrowingController extends Controller
{
    // Menampilkan riwayat transaksi DENGAN FILTER (Untuk Admin)
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'admin', 'book'])->latest();

        // 1. Filter Berdasarkan Pilihan Cepat (Harian/Bulanan/Tahunan)
        if ($request->has('filter') && $request->filter != '') {
            $now = Carbon::now();
            if ($request->filter == 'hari_ini') {
                $query->whereDate('borrow_date', $now->toDateString());
            } elseif ($request->filter == 'bulan_ini') {
                $query->whereMonth('borrow_date', $now->month)
                      ->whereYear('borrow_date', $now->year);
            } elseif ($request->filter == 'tahun_ini') {
                $query->whereYear('borrow_date', $now->year);
            }
        }

        // 2. Filter Berdasarkan Rentang Tanggal Kustom (Start Date - End Date)
        if ($request->has('start_date') && $request->start_date != '' && $request->has('end_date') && $request->end_date != '') {
            $query->whereBetween('borrow_date', [$request->start_date, $request->end_date]);
        }

        $borrowings = $query->get();
        return view('borrowings.index', compact('borrowings'));
    }

    public function create()
    {
        $members = User::where('role', 'anggota')->get();
        $books = Book::where('available_stock', '>', 0)->get();
        return view('borrowings.create', compact('members', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_stock < 1) {
            return back()->withErrors(['book_id' => 'Stok buku ini sedang kosong.']);
        }

        Borrowing::create([
            'user_id' => $request->user_id,
            'admin_id' => Auth::id(),
            'book_id' => $request->book_id,
            'borrow_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(7),
            'status' => 'borrowed',
        ]);

        $book->decrement('available_stock');
        return redirect('/borrowings')->with('success', 'Buku berhasil dipinjamkan!');
    }

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

        if ($now->greaterThan($dueDate)) {
            $daysLate = $dueDate->diffInDays($now);
            $finePerDay = 2000;
            $fineAmount = $daysLate * $finePerDay;
            $status = 'overdue'; 
        }

        $borrowing->update([
            'return_date' => $now,
            'status' => $status,
            'fine_amount' => $fineAmount,
        ]);

        $borrowing->book->increment('available_stock');

        $message = $fineAmount > 0 
            ? "Buku dikembalikan. Terdapat denda sebesar Rp" . number_format($fineAmount, 0, ',', '.') 
            : "Buku berhasil dikembalikan tepat waktu.";

        return redirect('/borrowings')->with('success', $message);
    }

    // Menghasilkan PDF yang MENGIKUTI FILTER yang sedang aktif
    public function reportPdf(Request $request)
    {
        $query = Borrowing::with(['user', 'admin', 'book'])->orderBy('borrow_date', 'desc');

        // Terapkan filter yang sama persis dengan tabel
        if ($request->has('filter') && $request->filter != '') {
            $now = Carbon::now();
            if ($request->filter == 'hari_ini') {
                $query->whereDate('borrow_date', $now->toDateString());
            } elseif ($request->filter == 'bulan_ini') {
                $query->whereMonth('borrow_date', $now->month)
                      ->whereYear('borrow_date', $now->year);
            } elseif ($request->filter == 'tahun_ini') {
                $query->whereYear('borrow_date', $now->year);
            }
        }

        if ($request->has('start_date') && $request->start_date != '' && $request->has('end_date') && $request->end_date != '') {
            $query->whereBetween('borrow_date', [$request->start_date, $request->end_date]);
        }

        $borrowings = $query->get();

        $pdf = Pdf::loadView('borrowings.report', compact('borrowings'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('laporan-sirkulasi-perpusapp.pdf');
    }

    public function history()
    {
        $borrowings = Borrowing::with(['book', 'admin'])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();
        return view('riwayat', compact('borrowings'));
    }
}