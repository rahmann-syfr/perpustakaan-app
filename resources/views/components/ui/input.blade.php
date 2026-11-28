@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'id' => null,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'rows' => 3,
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);
    $resolvedValue = old($name, $value);
    $borderClass = $hasError ? 'border-[#FF7A9C]' : 'border-[#0A0A0A]';
    $fieldClasses = "w-full bg-white text-[#0A0A0A] font-sans text-[15px] font-medium px-4 py-3 border-[3px] {$borderClass} rounded-none neo-shadow-md focus:outline-none focus:border-[#7B4DFF] focus:shadow-[4px_4px_0px_#7B4DFF] transition-colors disabled:bg-[#EBE7E7] disabled:cursor-not-allowed";
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $inputId }}" class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A]">
            {{ $label }}
            @if ($required)
                <span class="text-[#ba1a1a]">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $inputId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge(['class' => $fieldClasses]) }}
        >{{ $resolvedValue }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            @if ($required) required @endif
            {{ $attributes->merge(['class' => $fieldClasses]) }}
        >
            {{ $slot }}
        </select>
    @else
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $type === 'password' ? '' : $resolvedValue }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge(['class' => $fieldClasses]) }}
        />
    @endif

    @if ($hint && ! $hasError)
        <p class="font-sans text-[12px] text-[#4f4633] m-0">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="font-mono text-[11px] font-bold text-[#93000a] bg-[#ffdad6] border-[2px] border-[#0A0A0A] px-2.5 py-1 m-0">
            {{ $message }}
        </p>
    @enderror
</div>
