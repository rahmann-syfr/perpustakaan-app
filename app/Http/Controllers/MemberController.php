<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    /**
     * Daftar anggota dengan pencarian, filter, dan pagination.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:aktif,terlambat,nonaktif'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $status = $filters['status'] ?? null;

        $members = Member::query()
            ->with(['borrowings' => fn ($query) => $query->active()])
            ->withCount(['borrowings as active_borrowings_count' => fn ($query) => $query->active()])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'aktif', fn ($query) => $query->whereHas('borrowings', fn ($query) => $query->whereNull('return_date')))
            ->when($status === 'terlambat', fn ($query) => $query->whereHas(
                'borrowings',
                fn ($query) => $query->whereNull('return_date')
                    ->whereDate('due_date', '<', now()->toDateString())
            ))
            ->when($status === 'nonaktif', fn ($query) => $query->whereDoesntHave('borrowings'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'total' => Member::query()->count(),
            'active' => Borrowing::query()
                ->active()
                ->distinct('member_id')
                ->count('member_id'),
            'overdue' => Borrowing::query()
                ->overdue()
                ->distinct('member_id')
                ->count('member_id'),
        ];

        return view('members.index', compact('members', 'summary'));
    }

    /**
     * Form Tambah Anggota.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Simpan Anggota Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Member::create($validated);

        return redirect('/members')->with('success', 'Data anggota berhasil ditambahkan!');
    }

    /**
     * Detail Anggota (Beserta Riwayat Peminjamannya).
     */
    public function show(int $id): View
    {
        $member = Member::findOrFail($id);
        $borrowings = Borrowing::with('book')
            ->where('member_id', $member->id)
            ->latest()
            ->get();

        return view('members.show', compact('member', 'borrowings'));
    }

    /**
     * Form Edit Anggota.
     */
    public function edit(int $id): View
    {
        $member = Member::findOrFail($id);

        return view('members.edit', compact('member'));
    }

    /**
     * Update Data Anggota.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email,'.$member->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $member->update($validated);

        return redirect('/members')->with('success', 'Data anggota berhasil diperbarui!');
    }

    /**
     * Hapus Anggota.
     */
    public function destroy(int $id): RedirectResponse
    {
        $member = Member::findOrFail($id);

        if ($member->borrowings()->exists() || $member->finePayments()->exists()) {
            return redirect('/members')->with('error', 'Anggota masih memiliki riwayat peminjaman atau pembayaran denda sehingga tidak dapat dihapus.');
        }

        $member->delete();

        return redirect('/members')->with('success', 'Data anggota berhasil dihapus!');
    }
}
