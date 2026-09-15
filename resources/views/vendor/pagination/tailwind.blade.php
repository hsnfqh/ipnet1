@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-[12.5px] text-[#75727C] w-full">
        <div class="text-center sm:text-left">
            Menampilkan 
            <span class="font-medium text-[#17151C]">{{ $paginator->firstItem() ?? 0 }}</span> 
            &ndash; 
            <span class="font-medium text-[#17151C]">{{ $paginator->lastItem() ?? 0 }}</span> 
            dari 
            <span class="font-medium text-[#17151C]">{{ $paginator->total() }}</span> 
            data
        </div>

        <div class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="w-7 h-7 rounded-md border border-[#E7E5E3] text-[#3D3A44] opacity-30 cursor-not-allowed flex items-center justify-center select-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="w-7 h-7 rounded-md border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#3D3A44] flex items-center justify-center transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="w-7 h-7 flex items-center justify-center text-[12px] text-[#A19DA8] font-semibold select-none">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="w-7 h-7 rounded-md bg-[#AF1424] text-white border border-[#AF1424] text-[12px] font-bold flex items-center justify-center select-none shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-7 h-7 rounded-md border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#17151C] text-[12px] font-medium flex items-center justify-center transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="w-7 h-7 rounded-md border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#3D3A44] flex items-center justify-center transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="w-7 h-7 rounded-md border border-[#E7E5E3] text-[#3D3A44] opacity-30 cursor-not-allowed flex items-center justify-center select-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
