@extends('layouts.app')

@section('title', 'Tambah Buku - PerpusApp')
@section('page-title', 'Tambah Data Buku')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-ui.card :window="true" title="books.create" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b-[3px] border-[#0A0A0A]">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Form Tambah Buku Baru</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Lengkapi detail informasi buku dan unggah gambar sampul jika tersedia.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('books.index') }}">
                Kembali
            </x-ui.button>
        </div>

        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @include('books._form', ['book' => null])

            <div class="flex items-center justify-end gap-3 pt-4 border-t-[3px] border-[#0A0A0A]">
                <x-ui.button variant="secondary" href="{{ route('books.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="save">Simpan Buku</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
