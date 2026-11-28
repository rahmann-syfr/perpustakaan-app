@props([
    'label',
    'value',
    'icon' => 'menu_book',
    'color' => 'yellow',
    'badge' => null,
    'footerLeft' => null,
    'footerRight' => null,
    'href' => null,
])

@php
    $bgClass = match ($color) {
        'mint' => 'bg-[#A9D9BC]',
        'sky' => 'bg-[#4FA8FF]',
        'pink' => 'bg-[#FF7A9C]',
        'lime' => 'bg-[#C4F04D]',
        default => 'bg-[#F9C32E]',
    };
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "{$bgClass} border-[3px] border-[#0A0A0A] rounded-none neo-shadow-md p-5 flex flex-col justify-between transition-all hover:-translate-x-0.5 hover:-translate-y-1 text-[#0A0A0A] no-underline"]) }}
>
    <div>
        <div class="flex items-center justify-between mb-4 gap-2">
            <div class="w-10 h-10 border-[3px] border-[#0A0A0A] bg-white flex items-center justify-center neo-shadow-sm shrink-0">
                <span class="material-symbols-outlined text-[22px] text-[#0A0A0A]">{{ $icon }}</span>
            </div>
            @if ($badge)
                <span class="bg-white border-[2px] border-[#0A0A0A] px-2 py-0.5 font-mono text-[10px] font-bold text-[#0A0A0A] uppercase tracking-[0.04em]">
                    {{ $badge }}
                </span>
            @endif
        </div>
        <div class="font-mono text-[11px] font-bold text-[#0A0A0A] uppercase tracking-[0.04em] mb-1">{{ $label }}</div>
        <div class="font-display-lg text-[40px] lg:text-[44px] font-extrabold text-[#0A0A0A] leading-none mb-3 tracking-[-0.03em]">{{ $value }}</div>
    </div>

    @if ($footerLeft || $footerRight)
        <div class="pt-3 border-t-[2px] border-[#0A0A0A] flex items-center justify-between text-[#0A0A0A] gap-2">
            <span class="font-mono text-[10px] font-bold uppercase tracking-[0.04em]">{{ $footerLeft }}</span>
            <span class="font-mono text-[10px] font-bold uppercase tracking-[0.04em] underline">{{ $footerRight }}</span>
        </div>
    @endif
</{{ $tag }}>
