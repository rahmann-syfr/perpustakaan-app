@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
])

@php
    [$bgClass, $iconName, $defaultTitle] = match ($type) {
        'success' => ['bg-[#C4F04D]', 'check_circle', 'Berhasil'],
        'error', 'danger' => ['bg-[#FF7A9C]', 'error', 'Perhatian'],
        'warning' => ['bg-[#FF8A5B]', 'warning', 'Peringatan'],
        default => ['bg-[#4FA8FF]', 'info', 'Informasi'],
    };
@endphp

<div {{ $attributes->merge(['class' => "{$bgClass} border-[3px] border-[#0A0A0A] rounded-none neo-shadow-md p-4 flex items-start justify-between gap-4"]) }} role="alert" data-neo-alert>
    <div class="flex items-start gap-3 min-w-0">
        <div class="w-9 h-9 bg-white border-[3px] border-[#0A0A0A] flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[20px] text-[#0A0A0A]">{{ $iconName }}</span>
        </div>
        <div class="min-w-0">
            @if ($title ?? $defaultTitle)
                <p class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A] m-0">
                    {{ $title ?? $defaultTitle }}
                </p>
            @endif
            <div class="font-sans text-[14px] font-semibold text-[#0A0A0A] mt-0.5">
                {{ $slot }}
            </div>
        </div>
    </div>

    @if ($dismissible)
        <button
            type="button"
            onclick="this.closest('[data-neo-alert]')?.remove()"
            aria-label="Tutup pesan"
            class="w-7 h-7 bg-white border-[2px] border-[#0A0A0A] flex items-center justify-center shrink-0 cursor-pointer hover:bg-[#FFF6E0]"
        >
            <span class="material-symbols-outlined text-[16px] text-[#0A0A0A]">close</span>
        </button>
    @endif
</div>
