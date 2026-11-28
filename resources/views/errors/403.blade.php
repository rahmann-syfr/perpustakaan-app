@extends('layouts.guest')

@section('title', '403 Akses Ditolak - PerpusApp')

@section('content')
    <article class="w-full max-w-[600px] mx-auto bg-white border-[3px] border-[#0A0A0A] neo-shadow-lg flex flex-col justify-between">
        <div class="h-12 bg-[#ebe7e7] border-b-[3px] border-[#0A0A0A] px-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-[#FF7A9C] border border-[#0A0A0A] inline-block"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-[#F9C32E] border border-[#0A0A0A] inline-block"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-[#C4F04D] border border-[#0A0A0A] inline-block"></span>
                <span class="font-mono text-[10px] font-bold text-[#4f4633] uppercase tracking-wider ml-2">ERR_WINDOW_403</span>
            </div>
            <span class="font-mono text-[10px] bg-[#FF7A9C] text-[#0A0A0A] px-2 py-0.5 border border-[#0A0A0A] uppercase font-bold tracking-wider">HTTP 403 FORBIDDEN</span>
        </div>

        <div class="p-8 md:p-10 flex flex-col items-center text-center">
            <div class="relative my-4 flex items-center justify-center">
                <svg class="w-44 h-44 sm:w-52 sm:h-52 text-[#FF7A9C] drop-shadow-[4px_4px_0px_#0A0A0A]" fill="currentColor" viewBox="0 0 100 100" aria-hidden="true">
                    <polygon points="50,0 63,14 82,9 85,29 100,42 91,59 99,77 81,83 75,100 56,93 42,100 31,86 12,88 12,69 0,56 7,37 1,20 20,20 25,2 43,10" stroke="#0A0A0A" stroke-linejoin="miter" stroke-width="2.5"></polygon>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center -rotate-6 select-none">
                    <span class="font-display-lg text-[48px] text-[#0A0A0A] tracking-tighter font-extrabold leading-none">403</span>
                    <span class="font-mono text-[10px] uppercase bg-[#0A0A0A] text-white px-2 py-0.5 tracking-widest mt-1 border border-[#0A0A0A]">TERLARANG</span>
                </div>
                <div class="absolute -bottom-2 -right-2 rotate-3 bg-[#F9C32E] text-[#0A0A0A] font-mono text-[10px] font-bold px-2 py-1 border-[2px] border-[#0A0A0A] neo-shadow-sm">
                    RESTRICTED
                </div>
            </div>

            <h1 class="font-display-md text-[30px] sm:text-[36px] font-extrabold text-[#0A0A0A] uppercase tracking-tight mt-4 mb-2">
                Ups! Akses Ditolak
            </h1>
            <p class="font-sans text-[15px] text-[#4f4633] max-w-md m-0">
                {{ isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Akun Anda tidak memiliki hak akses untuk membuka halaman atau tindakan ini.' }}
            </p>
        </div>

        <div class="p-6 border-t-[3px] border-[#0A0A0A] bg-[#FFF6E0] flex justify-center">
            <x-ui.button variant="primary" icon="arrow_back" href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-full sm:w-auto">
                {{ auth()->check() ? 'Kembali ke Dashboard' : 'Kembali ke Login' }}
            </x-ui.button>
        </div>
    </article>
@endsection
