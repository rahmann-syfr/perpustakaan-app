@extends('layouts.app')

@section('title', 'Edit Anggota - PerpusApp')
@section('page-title', 'Edit Data Anggota')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-ui.card :window="true" title="members.edit" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b-[3px] border-[#0A0A0A]">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Form Edit Anggota</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Perbarui data <strong>{{ $member->name }}</strong>.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('members.index') }}">
                Kembali
            </x-ui.button>
        </div>

        <form action="{{ route('members.update', $member) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @include('members._form', ['member' => $member])

            <div class="flex items-center justify-end gap-3 pt-4 border-t-[3px] border-[#0A0A0A]">
                <x-ui.button variant="secondary" href="{{ route('members.show', $member) }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="save">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
