@extends('layouts.app')

@section('title', 'Edit Buku - PerpusApp')
@section('page-title', 'Edit Data Buku')

@section('content')
<div class="max-w-4xl mx-auto">
    <x-ui.card :window="true" title="books.edit" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b-[3px] border-[#0A0A0A]">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Form Edit Buku</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Perbarui informasi atau ganti sampul untuk buku <strong>{{ $book->title }}</strong>.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('books.index') }}">
                Kembali
            </x-ui.button>
        </div>

        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @include('books._form', ['book' => $book])

            <div class="flex items-center justify-end gap-3 pt-4 border-t-[3px] border-[#0A0A0A]">
                <x-ui.button variant="secondary" href="{{ route('books.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="save">Perbarui Buku</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
