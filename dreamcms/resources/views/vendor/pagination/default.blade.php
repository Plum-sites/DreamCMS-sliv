@if ($paginator->hasPages())
    <div class="paging">
        <ul class="paging_pages">
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="current_page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </ul>
        <ul class="paging_control">
            @if ($paginator->onFirstPage())
                <li class="prev_paging disabled_paging">
                    <span></span>
                </li>
            @else
                <li class="prev_paging">
                    <a style="padding: 0" href="{{ $paginator->previousPageUrl() }}" rel="prev"></a>
                </li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="next_paging">
                    <a style="padding: 0" href="{{ $paginator->nextPageUrl() }}" rel="next"></a>
                </li>
            @else
                <li class="next_paging disabled_paging">
                    <span></span>
                </li>
            @endif
        </ul>

    </div>
@endif
