@extends('layouts.app')

@section('title', 'Data Buku - PerpusApp')
@section('page-title', 'Manajemen Data Buku')

@section('content')
@php
    $totalTitles = \App\Models\Book::count();
    $totalCopies = (int) \App\Models\Book::sum('total_stock');
    $availableCopies = (int) \App\Models\Book::sum('available_stock');
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <x-ui.card :window="true" title="books.index" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Daftar Koleksi Buku</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Kelola stok, sampul, dan informasi bibliografi koleksi perpustakaan.
                </p>
            </div>
            <x-ui.button variant="primary" icon="add" href="{{ route('books.create') }}">
                Tambah Buku
            </x-ui.button>
        </div>
    </x-ui.card>

    {{-- 3 Kartu Ringkasan dari DB --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.stat-card
            color="yellow"
            icon="menu_book"
            badge="Judul"
            label="Total Judul Buku"
            :value="number_format($totalTitles, 0, ',', '.')"
        />
        <x-ui.stat-card
            color="mint"
            icon="inventory_2"
            badge="Total"
            label="Total Eksemplar"
            :value="number_format($totalCopies, 0, ',', '.')"
        />
        <x-ui.stat-card
            color="sky"
            icon="check_circle"
            badge="Siap Pinjam"
            label="Stok Tersedia"
            :value="number_format($availableCopies, 0, ',', '.')"
        />
    </div>

    {{-- Pencarian & Filter --}}
    <form action="{{ route('books.index') }}" method="GET" class="bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-5">
                <x-ui.input
                    name="search"
                    id="tableSearch"
                    label="Pencarian"
                    :value="request('search')"
                    placeholder="Cari judul, penulis, atau ISBN..."
                />
            </div>

            <div class="md:col-span-3">
                <x-ui.input name="category" id="bookCategoryFilter" type="select" label="Kategori" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-ui.input>
            </div>

            <div class="md:col-span-2">
                <x-ui.input name="stock_filter" id="bookStockFilter" type="select" label="Status Stok">
                    <option value="">Semua Stok</option>
                    <option value="available">Tersedia</option>
                    <option value="empty">Habis</option>
                </x-ui.input>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <x-ui.button type="submit" variant="primary" size="md" icon="search" class="w-full">
                    Cari
                </x-ui.button>
                @if (request('search') || request('category'))
                    <x-ui.button variant="secondary" size="md" href="{{ route('books.index') }}" title="Reset Filter">
                        Reset
                    </x-ui.button>
                @endif
            </div>
        </div>
    </form>

    {{-- Tabel Buku --}}
    @if ($books->isNotEmpty())
        <x-ui.table :headers="['Sampul', 'Judul & Penulis', 'Kategori', 'Tahun', 'Stok Total', 'Status Stok', 'Aksi']" id="booksTable">
            @foreach ($books as $book)
                @php
                    $stockStatus = $book->available_stock > 0 ? 'available' : 'empty';
                @endphp
                <tr data-book-row data-stock="{{ $stockStatus }}">
                    <td class="w-20">
                        @if ($book->cover_image)
                            <img
                                src="{{ asset('storage/' . $book->cover_image) }}"
                                alt="Sampul {{ $book->title }}"
                                class="w-12 h-16 object-cover border-[3px] border-[#0A0A0A] neo-shadow-sm"
                            />
                        @else
                            <div class="w-12 h-16 bg-[#FFF6E0] border-[3px] border-[#0A0A0A] neo-shadow-sm flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px] text-[#0A0A0A]">menu_book</span>
                            </div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('books.show', $book) }}" class="font-bold text-[#0A0A0A] hover:underline block">
                            {{ $book->title }}
                        </a>
                        <span class="font-sans text-[12px] text-[#4f4633] block mt-0.5">
                            {{ $book->author }}
                            @if ($book->isbn)
                                &bull; <span class="font-mono">ISBN: {{ $book->isbn }}</span>
                            @endif
                        </span>
                    </td>
                    <td>
                        <x-ui.badge variant="mint">{{ $book->category->name ?? '-' }}</x-ui.badge>
                    </td>
                    <td class="font-mono text-xs">{{ $book->published_year }}</td>
                    <td class="font-mono text-xs font-bold">{{ $book->total_stock }}</td>
                    <td>
                        @if ($book->available_stock > 0)
                            <x-ui.badge variant="success">{{ $book->available_stock }} Tersedia</x-ui.badge>
                        @else
                            <x-ui.badge variant="danger">Habis</x-ui.badge>
                        @endif
                    </td>
                    <td class="w-48">
                        <div class="flex items-center gap-1.5">
                            <x-ui.button variant="info" size="sm" href="{{ route('books.show', $book) }}" title="Detail">
                                Detail
                            </x-ui.button>
                            <x-ui.button variant="secondary" size="sm" href="{{ route('books.edit', $book) }}" title="Edit">
                                Edit
                            </x-ui.button>
                            <x-ui.button variant="danger" size="sm" data-modal-open="deleteBookModal{{ $book->id }}" title="Hapus">
                                Hapus
                            </x-ui.button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div id="tableEmpty" class="hidden">
            <x-ui.empty-state icon="filter_alt_off" title="Tidak Ada Buku yang Cocok" description="Tidak ada buku dengan status stok tersebut pada halaman ini." />
        </div>

        <x-ui.pagination :paginator="$books" />
    @else
        <div id="tableEmpty">
            <x-ui.empty-state
                icon="menu_book"
                title="Belum Ada Koleksi Buku"
                description="Mulai tambahkan data buku baru atau ubah kata kunci pencarian Anda."
            >
                <x-ui.button variant="primary" size="sm" icon="add" href="{{ route('books.create') }}">
                    Tambah Buku
                </x-ui.button>
            </x-ui.empty-state>
        </div>
    @endif
</div>
@endsection

@push('modals')
    @foreach ($books as $book)
        @php
            $activeBorrowCount = $book->activeBorrowings()->count();
        @endphp
        <x-ui.modal id="deleteBookModal{{ $book->id }}" title="Hapus Buku">
            @if ($activeBorrowCount > 0)
                <x-ui.alert type="error" :dismissible="false" title="Transaksi Aktif">
                    Buku <strong>{{ $book->title }}</strong> masih memiliki <strong>{{ $activeBorrowCount }} transaksi peminjaman aktif</strong> sehingga tidak dapat dihapus.
                </x-ui.alert>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteBookModal{{ $book->id }}">Tutup</x-ui.button>
                </x-slot:footer>
            @else
                <p class="font-sans text-[14px] text-[#0A0A0A] m-0">
                    Apakah Anda yakin ingin menghapus buku <strong>{{ $book->title }}</strong> beserta sampulnya?
                </p>
                <form id="deleteBookForm{{ $book->id }}" action="{{ route('books.destroy', $book) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteBookModal{{ $book->id }}">Batal</x-ui.button>
                    <x-ui.button type="submit" form="deleteBookForm{{ $book->id }}" variant="danger" size="sm" icon="delete">Hapus Buku</x-ui.button>
                </x-slot:footer>
            @endif
        </x-ui.modal>
    @endforeach
@endpush

@push('scripts')
<script>
    (function () {
        const stockFilter = document.getElementById('bookStockFilter');
        const rows = document.querySelectorAll('[data-book-row]');
        const tableWrap = document.getElementById('booksTable');
        const emptyBox = document.getElementById('tableEmpty');

        if (!stockFilter || !rows.length) return;

        stockFilter.addEventListener('change', function () {
            const val = stockFilter.value;
            let visible = 0;

            rows.forEach(function (row) {
                const match = !val || row.getAttribute('data-stock') === val;
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            if (tableWrap && emptyBox) {
                tableWrap.classList.toggle('hidden', visible === 0);
                emptyBox.classList.toggle('hidden', visible > 0);
            }
        });
    })();
</script>
@endpush
