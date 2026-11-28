@props([
    'icon' => 'inventory_2',
    'title' => 'Belum ada data',
    'description' => 'Data yang Anda cari belum tersedia saat ini.',
])

<div {{ $attributes->merge(['class' => 'bg-white border-[3px] border-[#0A0A0A] rounded-none neo-shadow-md p-8 text-center flex flex-col items-center justify-center gap-3']) }}>
    <div class="w-14 h-14 bg-[#FFF6E0] border-[3px] border-[#0A0A0A] neo-shadow-sm flex items-center justify-center">
        <span class="material-symbols-outlined text-[28px] text-[#0A0A0A]">{{ $icon }}</span>
    </div>
    <h3 class="font-headline-sm text-[18px] font-bold text-[#0A0A0A] m-0">{{ $title }}</h3>
    <p class="font-sans text-[14px] text-[#4f4633] max-w-md m-0">{{ $description }}</p>
    @if (trim((string) $slot) !== '')
        <div class="mt-2">
            {{ $slot }}
        </div>
    @endif
</div>
