@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="font-mono text-[11px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A]">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </div>

        <div class="flex items-center flex-wrap gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 bg-[#EBE7E7] text-[#4f4633] border-[3px] border-[#0A0A0A] font-mono text-[12px] font-bold uppercase cursor-not-allowed">
                    &larr; Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-1.5 bg-white text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-sm neo-shadow-hover font-mono text-[12px] font-bold uppercase transition-all">
                    &larr; Prev
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2.5 py-1.5 font-mono text-[12px] font-bold text-[#0A0A0A]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="px-3 py-1.5 bg-[#F9C32E] text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-sm font-mono text-[12px] font-bold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 bg-white text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-sm neo-shadow-hover font-mono text-[12px] font-bold transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-1.5 bg-white text-[#0A0A0A] border-[3px] border-[#0A0A0A] neo-shadow-sm neo-shadow-hover font-mono text-[12px] font-bold uppercase transition-all">
                    Next &rarr;
                </a>
            @else
                <span class="px-3 py-1.5 bg-[#EBE7E7] text-[#4f4633] border-[3px] border-[#0A0A0A] font-mono text-[12px] font-bold uppercase cursor-not-allowed">
                    Next &rarr;
                </span>
            @endif
        </div>
    </nav>
@endif
