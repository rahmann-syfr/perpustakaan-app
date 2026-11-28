<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
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
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
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
     * Form tambah anggota baru.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Menyimpan data anggota baru.
     */
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        Member::create($request->validated());

        return redirect()->route('members.index')
            ->with('success', 'Data anggota berhasil ditambahkan!');
    }

    /**
     * Detail anggota beserta riwayat peminjamannya.
     */
    public function show(Member $member): View
    {
        $borrowings = Borrowing::with('book')
            ->where('member_id', $member->id)
            ->latest()
            ->get();

        return view('members.show', compact('member', 'borrowings'));
    }

    /**
     * Form edit data anggota.
     */
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Memperbarui data anggota.
     */
    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $member->update($request->validated());

        return redirect()->route('members.index')
            ->with('success', 'Data anggota berhasil diperbarui!');
    }

    /**
     * Menghapus data anggota (ditolak jika masih memiliki peminjaman).
     */
    public function destroy(Member $member): RedirectResponse
    {
        if ($member->borrowings()->exists() || $member->finePayments()->exists()) {
            return redirect()->route('members.index')
                ->with('error', 'Anggota tidak dapat dihapus karena masih memiliki data peminjaman atau pembayaran denda.');
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Data anggota berhasil dihapus!');
    }
}
