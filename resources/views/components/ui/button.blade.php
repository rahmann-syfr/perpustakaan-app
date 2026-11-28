@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'bg-white text-[#0A0A0A]',
        'success' => 'bg-[#C4F04D] text-[#0A0A0A]',
        'danger' => 'bg-[#FF7A9C] text-[#0A0A0A]',
        'info' => 'bg-[#4FA8FF] text-[#0A0A0A]',
        default => 'bg-[#F9C32E] text-[#0A0A0A]',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-[11px]',
        'lg' => 'px-7 py-3.5 text-[14px]',
        default => 'px-6 py-3 text-[13px]',
    };

    $baseClasses = 'inline-flex items-center justify-center gap-2 border-[3px] border-[#0A0A0A] rounded-none font-mono font-bold uppercase tracking-[0.04em] neo-shadow-md neo-shadow-hover neo-shadow-active transition-all cursor-pointer select-none disabled:opacity-50 disabled:cursor-not-allowed';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$sizeClasses}"]) }}>
        @if ($icon)
            <span class="material-symbols-outlined text-[18px] leading-none">{{ $icon }}</span>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$sizeClasses}"]) }}>
        @if ($icon)
            <span class="material-symbols-outlined text-[18px] leading-none">{{ $icon }}</span>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
