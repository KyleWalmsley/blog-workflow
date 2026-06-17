@if ($paginator->hasPages())
    <nav style="display:flex;gap:8px;align-items:center;">
        @if ($paginator->onFirstPage())
            <span class="btn btn-muted btn-sm" style="opacity:0.4;">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-muted btn-sm">Previous</a>
        @endif

        <span style="font-size:12px;color:var(--text3);">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-muted btn-sm">Next</a>
        @else
            <span class="btn btn-muted btn-sm" style="opacity:0.4;">Next</span>
        @endif
    </nav>
@endif
