<?php

namespace App\Http\Controllers;

use App\Actions\BorrowBook;
use App\Actions\ExtendBorrowing;
use App\Actions\ReturnBook;
use App\Enums\BorrowingStatus;
use App\Enums\Role;
use App\Http\Requests\ExtendBorrowingRequest;
use App\Http\Requests\ReturnBorrowingRequest;
use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\SystemSetting;
use App\Models\User;
use App\Queries\BorrowingFilter;
use App\Support\BorrowingReportCsv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BorrowingController extends Controller
{
    /**
     * Menampilkan riwayat transaksi dengan filter (untuk admin/petugas).
     */
    public function index(Request $request): View
    {
        $filters = $this->validateFilters($request);

        $borrowings = BorrowingFilter::make($filters)
            ->apply(Borrowing::with(['user', 'admin', 'book'])->latest())
            ->paginate(15)
            ->withQueryString();

        // Statistik dihitung dari query yang sama dengan filter aktif agar
        // angka pada kartu ringkasan selalu konsisten dengan tabel.
        $stats = [
            'total' => $borrowings->total(),
            'dipinjam' => BorrowingFilter::make($filters)
                ->apply(Borrowing::query()->where('status', BorrowingStatus::Borrowed))
                ->count(),
            'terlambat' => BorrowingFilter::make($filters)
                ->apply(Borrowing::query()
                    ->active()
                    ->whereDate('due_date', '<', now()->toDateString()))
                ->count(),
            'selesai' => BorrowingFilter::make($filters)
                ->apply(Borrowing::query()->where('status', BorrowingStatus::Returned))
                ->count(),
            // Transaksi yang sudah lewat tenggat dihitung dari tanggal
            // kembali, bukan dari kolom status, karena status "overdue"
            // hanya dipakai oleh data lama.
            'telat_dikembalikan' => BorrowingFilter::make($filters)
                ->apply(Borrowing::query()->where('late_days', '>', 0))
                ->count(),
        ];

        return view('borrowings.index', compact('borrowings', 'filters', 'stats'));
    }

    /**
     * Menampilkan form peminjaman baru.
     */
    public function create(): View
    {
        $members = User::query()
            ->where('role', Role::Anggota->value)
            ->orderBy('name')
            ->get();

        $books = Book::query()
            ->where('available_stock', '>', 0)
            ->orderBy('title')
            ->get();

        $maxLoanDays = SystemSetting::getInt('maksimal_hari_pinjam', 7);

        return view('borrowings.create', compact('members', 'books', 'maxLoanDays'));
    }

    /**
     * Menyimpan transaksi peminjaman baru.
     */
    public function store(StoreBorrowingRequest $request, BorrowBook $borrowBook): RedirectResponse
    {
        $borrowBook->handle([
            'user_id' => $request->integer('user_id'),
            'book_id' => $request->integer('book_id'),
            'duration_days' => $request->integer('duration_days'),
        ], $request->user()->id);

        return redirect()->route('borrowings.index')
            ->with('success', 'Buku berhasil dipinjamkan!');
    }

    /**
     * Memproses pengembalian buku atau pencatatan buku hilang.
     */
    public function returnBook(
        ReturnBorrowingRequest $request,
        Borrowing $borrowing,
        ReturnBook $returnBook
    ): RedirectResponse {
        $status = BorrowingStatus::from($request->string('status')->toString());

        $updated = $returnBook->handle(
            $borrowing,
            $status,
            $request->filled('lost_fine_amount') ? $request->integer('lost_fine_amount') : null
        );

        $message = $updated->fine_amount > 0
            ? 'Status transaksi diperbarui. Terdapat tagihan/denda sebesar Rp'.number_format($updated->fine_amount, 0, ',', '.')
            : 'Buku berhasil dikembalikan tepat waktu.';

        return redirect()->route('borrowings.index')
            ->with('success', $message);
    }

    /**
     * Memperpanjang masa peminjaman buku.
     */
    public function extend(
        ExtendBorrowingRequest $request,
        Borrowing $borrowing,
        ExtendBorrowing $extendBorrowing
    ): RedirectResponse {
        $days = $request->integer('days');

        try {
            $updated = $extendBorrowing->handle($borrowing, $request->user()->id, $days);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()->route('borrowings.index')
            ->with('success', 'Peminjaman diperpanjang '.$days.' hari sampai '.$updated->due_date?->format('d M Y').'.');
    }

    /**
     * Menghasilkan PDF yang mengikuti filter yang sedang aktif.
     */
    public function reportPdf(Request $request): Response
    {
        $filters = $this->validateFilters($request);

        $borrowings = BorrowingFilter::make($filters)
            ->apply(Borrowing::with(['user', 'admin', 'book'])->orderByDesc('borrow_date'))
            ->get();

        $pdf = Pdf::loadView('borrowings.report', compact('borrowings', 'filters'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-sirkulasi-perpusapp.pdf');
    }

    /**
     * Mengekspor laporan sirkulasi ke CSV dengan filter yang sama seperti PDF.
     */
    public function reportCsv(Request $request): StreamedResponse
    {
        $filters = $this->validateFilters($request);

        $borrowings = BorrowingFilter::make($filters)
            ->apply(Borrowing::with(['user', 'admin', 'book'])->orderByDesc('borrow_date'))
            ->get();

        $filename = 'laporan-sirkulasi-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($borrowings): void {
            echo BorrowingReportCsv::render($borrowings);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Validasi filter laporan dan daftar sirkulasi.
     *
     * @return array<string, mixed>
     */
    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'filter' => ['nullable', Rule::in(['hari_ini', 'bulan_ini', 'tahun_ini'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', Rule::enum(BorrowingStatus::class)],
            'search' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
