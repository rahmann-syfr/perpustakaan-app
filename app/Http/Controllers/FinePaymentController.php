<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\PayFineRequest;
use App\Models\Borrowing;
use App\Models\FinePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinePaymentController extends Controller
{
    /**
     * Daftar seluruh pembayaran denda (hanya petugas/admin).
     */
    public function index(Request $request): View
    {
        $payments = FinePayment::query()
            ->with(['member', 'admin', 'borrowing.book'])
            ->when($request->filled('method'), fn ($query) => $query->where('method', $request->string('method')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => $payments->total(),
            'sum' => (int) FinePayment::query()->sum('amount'),
            'month' => (int) FinePayment::query()
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
        ];

        return view('fines.index', compact('payments', 'stats'));
    }

    /**
     * Halaman pembayaran denda untuk sebuah transaksi.
     */
    public function create(Borrowing $borrowing): View
    {
        abort_unless($borrowing->hasOutstandingFine(), 404);

        return view('fines.create', [
            'borrowing' => $borrowing->load(['member', 'book']),
            'outstanding' => $borrowing->outstandingFine(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    /**
     * Mencatat pembayaran denda untuk sebuah transaksi.
     */
    public function store(PayFineRequest $request, Borrowing $borrowing): RedirectResponse
    {
        $payment = $request->payFine($borrowing);

        return back()->with('success', 'Pembayaran denda sebesar Rp'.number_format($payment->amount, 0, ',', '.').' berhasil dicatat.');
    }
}
