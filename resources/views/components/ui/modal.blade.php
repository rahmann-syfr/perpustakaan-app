@props([
    'id',
    'title' => 'Dialog',
    'size' => 'md',
])

@php
    $maxWidth = match ($size) {
        'sm' => 'max-w-md',
        'lg' => 'max-w-3xl',
        'xl' => 'max-w-5xl',
        default => 'max-w-xl',
    };
@endphp

<div
    id="{{ $id }}"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0A0A0A]/60 p-4"
    data-neo-modal
>
    <div class="w-full {{ $maxWidth }} bg-white border-[3px] border-[#0A0A0A] rounded-none neo-shadow-xl overflow-hidden">
        <div class="bg-[#0A0A0A] text-white px-4 py-2.5 flex items-center justify-between border-b-[3px] border-[#0A0A0A]">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-[#FF7A9C] border border-[#0A0A0A] shrink-0"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-[#F9C32E] border border-[#0A0A0A] shrink-0"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-[#C4F04D] border border-[#0A0A0A] shrink-0"></span>
                <h2 id="{{ $id }}-title" class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] ml-2 truncate m-0">
                    {{ $title }}
                </h2>
            </div>
            <button
                type="button"
                data-modal-close="{{ $id }}"
                aria-label="Tutup dialog"
                class="w-7 h-7 bg-[#FF7A9C] text-[#0A0A0A] border-[2px] border-white flex items-center justify-center font-mono font-bold cursor-pointer hover:bg-[#F9C32E]"
            >
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>

        <div class="p-6 bg-white text-[#0A0A0A]">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-6 py-4 bg-[#FFF6E0] border-t-[3px] border-[#0A0A0A] flex items-center justify-end gap-3 flex-wrap">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
