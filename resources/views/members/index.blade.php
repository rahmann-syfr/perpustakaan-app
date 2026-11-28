@extends('layouts.app')

@section('title', 'Manajemen Anggota - PerpusApp')
@section('page-title', 'Data Anggota Perpustakaan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <x-ui.card :window="true" title="members.index" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Daftar Anggota</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Kelola data anggota perpustakaan yang terdaftar di sistem.
                </p>
            </div>
            <x-ui.button variant="primary" icon="person_add" href="{{ route('members.create') }}">
                Tambah Anggota
            </x-ui.button>
        </div>
    </x-ui.card>

    {{-- 3 Kartu Ringkasan dari DB --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.stat-card
            color="yellow"
            icon="groups"
            badge="Terdaftar"
            label="Total Anggota"
            :value="number_format($summary['total'], 0, ',', '.')"
        />
        <x-ui.stat-card
            color="mint"
            icon="menu_book"
            badge="Aktif"
            label="Sedang Meminjam"
            :value="number_format($summary['active'], 0, ',', '.')"
        />
        <x-ui.stat-card
            color="pink"
            icon="schedule"
            badge="Perhatian"
            label="Memiliki Tunggakan"
            :value="number_format($summary['overdue'], 0, ',', '.')"
        />
    </div>

    {{-- Pencarian & Filter --}}
    <form action="{{ route('members.index') }}" method="GET" class="bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-6">
                <x-ui.input
                    name="search"
                    id="tableSearch"
                    label="Pencarian"
                    :value="request('search')"
                    placeholder="Cari nama, email, atau no HP..."
                />
            </div>

            <div class="md:col-span-4">
                <x-ui.input name="status" id="memberStatusFilter" type="select" label="Status Pinjaman" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Sedang Meminjam</option>
                    <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Bebas Pinjaman</option>
                </x-ui.input>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <x-ui.button type="submit" variant="primary" size="md" icon="search" class="w-full">
                    Cari
                </x-ui.button>
                @if (request('search') || request('status'))
                    <x-ui.button variant="secondary" size="md" href="{{ route('members.index') }}">
                        Reset
                    </x-ui.button>
                @endif
            </div>
        </div>
    </form>

    {{-- Tabel Anggota --}}
    @if ($members->isNotEmpty())
        <x-ui.table :headers="['No', 'Anggota', 'Email', 'No HP', 'Status Pinjaman', 'Terdaftar', 'Aksi']">
            @foreach ($members as $index => $member)
                @php
                    $activeCount = (int) $member->active_borrowings_count;
                    $initial = strtoupper(mb_substr($member->name, 0, 1));
                @endphp
                <tr>
                    <td class="font-mono text-xs w-14">{{ $members->firstItem() + $index }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full border-[2px] border-[#0A0A0A] bg-[#F9C32E] flex items-center justify-center font-mono text-sm font-bold text-[#0A0A0A] shrink-0">
                                {{ $initial }}
                            </div>
                            <a href="{{ route('members.show', $member) }}" class="font-bold text-[#0A0A0A] hover:underline">
                                {{ $member->name }}
                            </a>
                        </div>
                    </td>
                    <td class="font-mono text-xs">{{ $member->email }}</td>
                    <td class="font-mono text-xs">{{ $member->phone ?: '-' }}</td>
                    <td>
                        @if ($activeCount > 0)
                            <x-ui.badge variant="warning">{{ $activeCount }} Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="success">Bebas</x-ui.badge>
                        @endif
                    </td>
                    <td class="font-mono text-xs">{{ $member->created_at?->format('d/m/Y') ?? '-' }}</td>
                    <td class="w-48">
                        <div class="flex items-center gap-1.5">
                            <x-ui.button variant="info" size="sm" href="{{ route('members.show', $member) }}">Detail</x-ui.button>
                            <x-ui.button variant="secondary" size="sm" href="{{ route('members.edit', $member) }}">Edit</x-ui.button>
                            <x-ui.button variant="danger" size="sm" data-modal-open="deleteMemberModal{{ $member->id }}">Hapus</x-ui.button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div id="tableEmpty" class="hidden"></div>

        <x-ui.pagination :paginator="$members" />
    @else
        <div id="tableEmpty">
            <x-ui.empty-state
                icon="badge"
                :title="(request('search') || request('status')) ? 'Tidak Ada Anggota yang Cocok' : 'Belum Ada Anggota'"
                :description="(request('search') || request('status')) ? 'Ubah kata kunci pencarian atau reset filter untuk melihat daftar lengkap.' : 'Tambahkan anggota pertama untuk mulai layanan peminjaman.'"
            >
                <div class="flex items-center justify-center gap-2">
                    @if (request('search') || request('status'))
                        <x-ui.button variant="secondary" size="sm" href="{{ route('members.index') }}">Reset Filter</x-ui.button>
                    @endif
                    <x-ui.button variant="primary" size="sm" icon="person_add" href="{{ route('members.create') }}">Tambah Anggota</x-ui.button>
                </div>
            </x-ui.empty-state>
        </div>
    @endif
</div>
@endsection

@push('modals')
    @foreach ($members as $member)
        @php
            $hasBorrowings = $member->borrowings()->exists();
            $hasActive = $member->active_borrowings_count > 0;
        @endphp
        <x-ui.modal id="deleteMemberModal{{ $member->id }}" title="Hapus Anggota">
            @if ($hasBorrowings)
                <x-ui.alert type="error" :dismissible="false" title="Tidak Dapat Dihapus">
                    Anggota <strong>{{ $member->name }}</strong> masih memiliki {{ $hasActive ? 'pinjaman aktif' : 'riwayat data peminjaman' }} di sistem sehingga tidak dapat dihapus.
                </x-ui.alert>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteMemberModal{{ $member->id }}">Tutup</x-ui.button>
                </x-slot:footer>
            @else
                <p class="font-sans text-[14px] text-[#0A0A0A] m-0">
                    Apakah Anda yakin ingin menghapus data anggota <strong>{{ $member->name }}</strong>?
                </p>
                <form id="deleteMemberForm{{ $member->id }}" action="{{ route('members.destroy', $member) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteMemberModal{{ $member->id }}">Batal</x-ui.button>
                    <x-ui.button type="submit" form="deleteMemberForm{{ $member->id }}" variant="danger" size="sm" icon="delete">Hapus Anggota</x-ui.button>
                </x-slot:footer>
            @endif
        </x-ui.modal>
    @endforeach
@endpush
