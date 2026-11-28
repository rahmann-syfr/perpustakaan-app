@extends('layouts.app')

@section('title', 'Edit Kategori - PerpusApp')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="max-w-xl mx-auto">
    <x-ui.card :window="true" title="categories.edit" bodyClass="p-6">
        <div class="flex items-center justify-between gap-4 pb-4 mb-6 border-b-[3px] border-[#0A0A0A]">
            <div>
                <h2 class="font-headline-md text-[20px] font-extrabold text-[#0A0A0A] m-0">Edit Kategori</h2>
                <p class="font-sans text-[13px] text-[#4f4633] mt-1 mb-0">Perbarui nama kategori buku.</p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('categories.index') }}">
                Kembali
            </x-ui.button>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <x-ui.input
                name="name"
                label="Nama Kategori"
                :value="$category->name"
                :required="true"
            />

            <div class="flex items-center justify-end gap-3 pt-4 border-t-[3px] border-[#0A0A0A]">
                <x-ui.button variant="secondary" href="{{ route('categories.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="save">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
