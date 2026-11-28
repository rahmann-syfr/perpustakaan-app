@extends('layouts.app')

@section('title', 'Detail Buku - PerpusApp')
@section('page-title', 'Detail Informasi Buku')

@section('content')
@php
    $borrowedCount = $book->activeBorrowings()->count();
@endphp

<div class="space-y-6 max-w-5xl mx-auto">
    <x-ui.card :window="true" title="books.show" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b-[3px] border-[#0A0A0A]">
            <div>
                <h2 class="font-headline-lg text-[24px] font-extrabold text-[#0A0A0A] m-0">{{ $book->title }}</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">Informasi lengkap koleksi buku.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <x-ui.button variant="secondary" size="sm" icon="arrow_back" href="{{ route('books.index') }}">
                    Kembali
                </x-ui.button>
                <x-ui.button variant="primary" size="sm" icon="edit" href="{{ route('books.edit', $book) }}">
                    Edit Buku
                </x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            <div class="md:col-span-4 flex justify-center">
                @if ($book->cover_image)
                    <img
                        src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="Sampul {{ $book->title }}"
                        class="w-full max-w-[260px] h-80 object-cover border-[3px] border-[#0A0A0A] neo-shadow-lg"
                    />
                @else
                    <div class="w-full max-w-[260px] h-80 bg-[#FFF6E0] border-[3px] border-[#0A0A0A] neo-shadow-lg flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[48px] text-[#0A0A0A]">menu_book</span>
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">Tanpa Sampul</span>
                    </div>
                @endif
            </div>

            <div class="md:col-span-8 space-y-6">
                <div class="border-[3px] border-[#0A0A0A] bg-white">
                    <div class="flex items-center justify-between px-4 py-3 border-b-[2px] border-[#0A0A0A] bg-[#FFF6E0]">
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">Kategori</span>
                        <x-ui.badge variant="mint">{{ $book->category->name ?? 'Tanpa Kategori' }}</x-ui.badge>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 border-b-[2px] border-[#0A0A0A]">
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">Penulis</span>
                        <span class="font-sans text-[14px] font-bold text-[#0A0A0A]">{{ $book->author }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 border-b-[2px] border-[#0A0A0A] bg-[#FFF6E0]">
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">Penerbit</span>
                        <span class="font-sans text-[14px] font-medium text-[#0A0A0A]">{{ $book->publisher ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 border-b-[2px] border-[#0A0A0A]">
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">ISBN</span>
                        <code class="font-mono text-[13px] font-bold text-[#0A0A0A]">{{ $book->isbn }}</code>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">Tahun Terbit</span>
                        <span class="font-mono text-[14px] font-bold text-[#0A0A0A]">{{ $book->published_year }}</span>
                    </div>
                </div>

                {{-- Kotak INFO PEMINJAMAN (3 kotak seimbang, tanpa denda) --}}
                <div class="space-y-3">
                    <h3 class="font-mono text-[12px] font-bold uppercase tracking-wider text-[#0A0A0A] m-0">
                        Info Peminjaman
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-[#F9C32E] border-[3px] border-[#0A0A0A] neo-shadow-sm p-4">
                            <span class="font-mono text-[10px] font-bold uppercase text-[#0A0A0A] block">Stok Total</span>
                            <span class="font-display-md text-[32px] font-extrabold text-[#0A0A0A] leading-tight">{{ $book->total_stock }}</span>
                        </div>
                        <div class="bg-[#C4F04D] border-[3px] border-[#0A0A0A] neo-shadow-sm p-4">
                            <span class="font-mono text-[10px] font-bold uppercase text-[#0A0A0A] block">Tersedia</span>
                            <span class="font-display-md text-[32px] font-extrabold text-[#0A0A0A] leading-tight">{{ $book->available_stock }}</span>
                        </div>
                        <div class="bg-[#4FA8FF] border-[3px] border-[#0A0A0A] neo-shadow-sm p-4">
                            <span class="font-mono text-[10px] font-bold uppercase text-[#0A0A0A] block">Sedang Dipinjam</span>
                            <span class="font-display-md text-[32px] font-extrabold text-[#0A0A0A] leading-tight">{{ $borrowedCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
@endsection
