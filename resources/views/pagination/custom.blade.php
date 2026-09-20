@if ($paginator->hasPages())
    <nav class="pagination-nav">

        @if ($paginator->onFirstPage())
            <span class="page-link page-link-disabled">قبلی</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link">قبلی</a>
        @endif

        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
            @if ($page == $paginator->currentPage())
                <span class="page-link page-link-active">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="page-link">{{ $page }}</a>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link">بعدی</a>
        @else
            <span class="page-link page-link-disabled">بعدی</span>
        @endif

    </nav>
@endif