@props([
    'headers' => [],
])

<div {{ $attributes->merge(['class' => 'bg-white border-[3px] border-[#0A0A0A] rounded-none neo-shadow-lg overflow-x-auto']) }}>
    <table class="w-full border-collapse text-left">
        @if (count($headers) > 0 || isset($head))
            <thead>
                <tr class="bg-[#F9C32E] border-b-[3px] border-[#0A0A0A]">
                    @isset($head)
                        {{ $head }}
                    @else
                        @foreach ($headers as $header)
                            <th class="px-4 py-3 font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A] border-r-[3px] border-[#0A0A0A] last:border-r-0 whitespace-nowrap">
                                {{ $header }}
                            </th>
                        @endforeach
                    @endisset
                </tr>
            </thead>
        @endif

        <tbody class="[&>tr]:border-b-[3px] [&>tr]:border-[#0A0A0A] [&>tr:last-child]:border-b-0 [&>tr:nth-child(odd)]:bg-white [&>tr:nth-child(even)]:bg-[#FFF6E0] [&>tr]:transition-colors [&>tr:hover]:bg-[#F3B6F5] [&>tr>td]:px-4 [&>tr>td]:py-3 [&>tr>td]:border-r-[3px] [&>tr>td]:border-[#0A0A0A] [&>tr>td:last-child]:border-r-0 [&>tr>td]:text-[14px] [&>tr>td]:font-medium [&>tr>td]:text-[#0A0A0A]">
            {{ $slot }}
        </tbody>
    </table>
</div>
