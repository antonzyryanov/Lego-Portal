@props(['paginator'])

@if ($paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
    <nav class="pagination-wrap" aria-label="Pagination">
        <ul class="pagination">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="disabled"><span aria-disabled="true">Prev</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Prev</a></li>
            @endif

            {{-- Page numbers (window around current) --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp

            @if ($start > 1)
                <li><a href="{{ $paginator->url(1) }}">1</a></li>
                @if ($start > 2)
                    <li class="disabled"><span>…</span></li>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $paginator->currentPage())
                    <li class="active"><span aria-current="page">{{ $page }}</span></li>
                @else
                    <li><a href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                @endif
            @endfor

            @if ($end < $paginator->lastPage())
                @if ($end < $paginator->lastPage() - 1)
                    <li class="disabled"><span>…</span></li>
                @endif
                <li><a href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
            @endif

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a></li>
            @else
                <li class="disabled"><span aria-disabled="true">Next</span></li>
            @endif
        </ul>
    </nav>
@elseif ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages())
    <nav class="pagination-wrap" aria-label="Pagination">
        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="disabled"><span aria-disabled="true">Prev</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Prev</a></li>
            @endif
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a></li>
            @else
                <li class="disabled"><span aria-disabled="true">Next</span></li>
            @endif
        </ul>
    </nav>
@endif
