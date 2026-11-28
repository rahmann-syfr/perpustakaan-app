@props([
    'href',
    'icon',
    'label',
    'active' => false,
])

<a
    href="{{ $href }}"
    class="perpus-nav-item {{ $active ? 'active bg-[#0A0A0A] text-white shadow-none' : 'bg-white text-[#0A0A0A] neo-shadow-hover' }} flex items-center border-[3px] border-[#0A0A0A] transition-all text-decoration-none"
>
    <div class="w-10 h-10 border-r-[3px] border-[#0A0A0A] {{ $active ? 'bg-[#F9C32E]' : 'bg-[#FFF6E0]' }} flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-[20px] text-[#0A0A0A]">{{ $icon }}</span>
    </div>
    <span class="font-headline-sm text-xs font-bold px-3 py-2 truncate">{{ $label }}</span>
</a>
