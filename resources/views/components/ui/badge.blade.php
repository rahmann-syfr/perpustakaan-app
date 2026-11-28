@props([
    'status' => null,
    'variant' => 'neutral',
    'dot' => false,
    'rotate' => null,
])

@php
    $normalizedStatus = $status ? strtoupper((string) $status) : null;

    [$colorClasses, $labelText] = match ($normalizedStatus) {
        'DIPINJAM', 'BORROWED' => ['bg-[#F9C32E] text-[#0A0A0A]', 'DIPINJAM'],
        'DIKEMBALIKAN', 'RETURNED' => ['bg-[#C4F04D] text-[#0A0A0A]', 'DIKEMBALIKAN'],
        'TERLAMBAT', 'OVERDUE' => ['bg-[#FF7A9C] text-[#0A0A0A]', 'TERLAMBAT'],
        'HILANG', 'LOST' => ['bg-[#0A0A0A] text-white', 'HILANG'],
        default => [
            match ($variant) {
                'primary', 'yellow' => 'bg-[#F9C32E] text-[#0A0A0A]',
                'success', 'lime' => 'bg-[#C4F04D] text-[#0A0A0A]',
                'danger', 'pink' => 'bg-[#FF7A9C] text-[#0A0A0A]',
                'warning', 'orange' => 'bg-[#FF8A5B] text-[#0A0A0A]',
                'info', 'sky' => 'bg-[#4FA8FF] text-[#0A0A0A]',
                'mint' => 'bg-[#A9D9BC] text-[#0A0A0A]',
                'violet' => 'bg-[#7B4DFF] text-white',
                'dark' => 'bg-[#0A0A0A] text-white',
                default => 'bg-white text-[#0A0A0A]',
            },
            null,
        ],
    };

    $rotateClass = match ($rotate) {
        'left' => '-rotate-3',
        'right' => 'rotate-3',
        default => '',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 border-[3px] border-[#0A0A0A] rounded-none px-2.5 py-1 font-mono text-[11px] font-bold uppercase tracking-[0.04em] leading-none {$colorClasses} {$rotateClass}"]) }}>
    @if ($dot)
        <span class="w-2 h-2 rounded-full bg-current shrink-0"></span>
    @endif
    <span>{{ trim((string) $slot) !== '' ? $slot : ($labelText ?? '') }}</span>
</span>
