@extends('layouts.app')

@section('title', 'Kategori Buku - PerpusApp')
@section('page-title', 'Manajemen Kategori')

@section('content')
@php
    $isAdmin = auth()->user()->role === \App\Enums\Role::Admin;
@endphp

<div class="space-y-6">
    <x-ui.card :window="true" title="categories.index" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Daftar Kategori Buku</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Kelola pengelompokan koleksi buku perpustakaan.
                </p>
            </div>

            @if ($isAdmin)
                <x-ui.button variant="primary" icon="add" data-modal-open="addCategoryModal">
                    Tambah Kategori
                </x-ui.button>
            @else
                <x-ui.badge variant="info" icon="lock">
                    Hanya Admin yang dapat mengelola kategori
                </x-ui.badge>
            @endif
        </div>
    </x-ui.card>

    <div class="bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative flex-1">
            <input
                type="text"
                id="tableSearch"
                placeholder="Cari nama kategori pada halaman ini..."
                autocomplete="off"
                class="w-full bg-white text-[#0A0A0A] font-sans text-[14px] px-4 py-2.5 border-[3px] border-[#0A0A0A] neo-shadow-sm focus:outline-none focus:border-[#7B4DFF]"
            />
        </div>
        <span id="categoryCounter" class="font-mono text-[11px] font-bold uppercase text-[#4f4633] shrink-0">
            {{ $categories->count() }} kategori ditampilkan
        </span>
    </div>

    @if ($categories->isNotEmpty())
        <x-ui.table :headers="$isAdmin ? ['No', 'Nama Kategori', 'Jumlah Buku', 'Aksi'] : ['No', 'Nama Kategori', 'Jumlah Buku']" id="categoryTable">
            @foreach ($categories as $index => $category)
                @php
                    $bookCount = $category->books()->count();
                @endphp
                <tr data-category-row data-name="{{ mb_strtolower($category->name) }}">
                    <td class="font-mono text-xs w-16">{{ ($categories->firstItem() ?? 1) + $index }}</td>
                    <td class="font-bold">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 bg-[#FFF6E0] border-[2px] border-[#0A0A0A] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">bookmarks</span>
                            </span>
                            <span>{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="font-mono text-xs">
                        <x-ui.badge variant="mint">{{ $bookCount }} BUKU</x-ui.badge>
                    </td>
                    @if ($isAdmin)
                        <td class="w-48">
                            <div class="flex items-center gap-2">
                                <x-ui.button variant="secondary" size="sm" icon="edit" data-modal-open="editCategoryModal{{ $category->id }}">
                                    Edit
                                </x-ui.button>
                                <x-ui.button variant="danger" size="sm" icon="delete" data-modal-open="deleteCategoryModal{{ $category->id }}">
                                    Hapus
                                </x-ui.button>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </x-ui.table>

        <div id="tableEmpty" class="hidden">
            <x-ui.empty-state icon="search_off" title="Kategori Tidak Ditemukan" description="Tidak ada kategori yang cocok dengan kata kunci pencarian Anda." />
        </div>

        <x-ui.pagination :paginator="$categories" />
    @else
        <div id="tableEmpty">
            <x-ui.empty-state
                icon="bookmarks"
                title="Belum Ada Kategori"
                :description="$isAdmin ? 'Tambahkan kategori pertama untuk mulai mengelompokkan koleksi buku.' : 'Belum ada data kategori buku yang tersedia.'"
            >
                @if ($isAdmin)
                    <x-ui.button variant="primary" size="sm" icon="add" data-modal-open="addCategoryModal">
                        Tambah Kategori
                    </x-ui.button>
                @endif
            </x-ui.empty-state>
        </div>
    @endif
</div>
@endsection

@if ($isAdmin)
@push('modals')
    {{-- Modal Tambah Kategori --}}
    <x-ui.modal id="addCategoryModal" title="Tambah Kategori Baru">
        <form id="addCategoryForm" action="{{ route('categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <x-ui.input
                name="name"
                id="add_category_name"
                label="Nama Kategori"
                placeholder="Contoh: Pemrograman Web"
                :required="true"
            />
        </form>
        <x-slot:footer>
            <x-ui.button variant="secondary" size="sm" data-modal-close="addCategoryModal">Batal</x-ui.button>
            <x-ui.button type="submit" form="addCategoryForm" variant="primary" size="sm" icon="save">Simpan</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Modal Edit & Hapus per Kategori --}}
    @foreach ($categories as $category)
        @php
            $bookCount = $category->books()->count();
        @endphp

        <x-ui.modal id="editCategoryModal{{ $category->id }}" title="Edit Kategori">
            <form id="editCategoryForm{{ $category->id }}" action="{{ route('categories.update', $category) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <x-ui.input
                    name="name"
                    id="edit_category_name_{{ $category->id }}"
                    label="Nama Kategori"
                    :value="$category->name"
                    :required="true"
                />
            </form>
            <x-slot:footer>
                <x-ui.button variant="secondary" size="sm" data-modal-close="editCategoryModal{{ $category->id }}">Batal</x-ui.button>
                <x-ui.button type="submit" form="editCategoryForm{{ $category->id }}" variant="primary" size="sm" icon="save">Simpan Perubahan</x-ui.button>
            </x-slot:footer>
        </x-ui.modal>

        <x-ui.modal id="deleteCategoryModal{{ $category->id }}" title="Hapus Kategori">
            @if ($bookCount > 0)
                <x-ui.alert type="error" :dismissible="false" title="Tidak Dapat Dihapus">
                    Kategori <strong>{{ $category->name }}</strong> masih memiliki <strong>{{ $bookCount }} buku</strong>. Pindahkan atau hapus buku terkait terlebih dahulu.
                </x-ui.alert>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteCategoryModal{{ $category->id }}">Tutup</x-ui.button>
                </x-slot:footer>
            @else
                <p class="font-sans text-[14px] text-[#0A0A0A] m-0">
                    Apakah Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="deleteCategoryForm{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteCategoryModal{{ $category->id }}">Batal</x-ui.button>
                    <x-ui.button type="submit" form="deleteCategoryForm{{ $category->id }}" variant="danger" size="sm" icon="delete">Hapus Kategori</x-ui.button>
                </x-slot:footer>
            @endif
        </x-ui.modal>
    @endforeach
@endpush
@endif

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('tableSearch');
        const rows = document.querySelectorAll('[data-category-row]');
        const counter = document.getElementById('categoryCounter');
        const emptyBox = document.getElementById('tableEmpty');
        const tableWrap = document.getElementById('categoryTable');

        if (!input || !rows.length) return;

        input.addEventListener('input', function () {
            const q = input.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                const name = row.getAttribute('data-name') || '';
                const match = !q || name.includes(q);
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            if (counter) {
                counter.textContent = visible + ' kategori ditampilkan';
            }
            if (emptyBox && tableWrap) {
                emptyBox.classList.toggle('hidden', visible > 0);
                tableWrap.classList.toggle('hidden', visible === 0);
            }
        });
    })();
</script>
@endpush
