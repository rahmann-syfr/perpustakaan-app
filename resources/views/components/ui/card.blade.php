@props([
    'window' => false,
    'title' => null,
    'rightLabel' => null,
    'sticker' => null,
    'bodyClass' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'relative bg-white border-[3px] border-[#0A0A0A] rounded-none neo-shadow-lg']) }}>
    @if ($sticker)
        <div class="absolute -top-4 -right-3 z-10 rotate-3 bg-[#F9C32E] border-[3px] border-[#0A0A0A] neo-shadow-sm px-3 py-1 font-mono text-[10px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A]">
            {{ $sticker }}
        </div>
    @endif

    @if ($window)
        <div class="bg-[#0A0A0A] text-white px-4 py-2.5 flex items-center justify-between border-b-[3px] border-[#0A0A0A]">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-[#FF7A9C] border border-[#0A0A0A] shrink-0"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-[#F9C32E] border border-[#0A0A0A] shrink-0"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-[#C4F04D] border border-[#0A0A0A] shrink-0"></span>
                @if ($title)
                    <span class="font-mono text-[11px] font-bold uppercase tracking-[0.04em] ml-2 truncate text-white">{{ $title }}</span>
                @endif
            </div>
            @if ($rightLabel)
                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.04em] text-white/75 shrink-0">{{ $rightLabel }}</span>
            @endif
        </div>
    @elseif ($title)
        <div class="px-6 py-4 bg-[#FFF6E0] border-b-[3px] border-[#0A0A0A] flex items-center justify-between gap-4">
            <h3 class="font-headline-sm text-[18px] font-bold text-[#0A0A0A] m-0">{{ $title }}</h3>
            @if ($rightLabel)
                <span class="font-mono text-[11px] font-bold uppercase tracking-[0.04em] text-[#4f4633]">{{ $rightLabel }}</span>
            @endif
        </div>
    @endif

    <div class="{{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
