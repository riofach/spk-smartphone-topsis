@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center align-items-center gap-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button class="pagination-arrow disabled" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
        @else
            <button class="pagination-arrow" wire:click="previousPage" wire:loading.attr="disabled"
                onclick="window.location='{{ $paginator->previousPageUrl() }}'">
                <i class="fas fa-chevron-left"></i>
            </button>
        @endif

        <div class="pagination-numbers d-flex align-items-center gap-2">
            {{-- First Page Link
            <a href="{{ $paginator->url(1) }}"
                class="pagination-number {{ $paginator->currentPage() === 1 ? 'active' : '' }}">
                1
            </a> --}}

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-number active">{{ $page }}</span>
                        @elseif ($page === $paginator->currentPage() - 1 || $page === $paginator->currentPage() + 1)
                            <a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
                        @elseif ($page === $paginator->currentPage() - 2 || $page === $paginator->currentPage() + 2)
                            <span class="pagination-ellipsis">...</span>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Last Page Link --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="pagination-number">
                    {{ $paginator->lastPage() }}
                </a>
            @endif
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button class="pagination-arrow" wire:click="nextPage" wire:loading.attr="disabled"
                onclick="window.location='{{ $paginator->nextPageUrl() }}'">
                <i class="fas fa-chevron-right"></i>
            </button>
        @else
            <button class="pagination-arrow disabled" disabled>
                <i class="fas fa-chevron-right"></i>
            </button>
        @endif

        {{-- Go to page input --}}
        {{-- <div class="go-to-page d-flex align-items-center gap-2">
            <span class="text-white">Go to page</span>
            <div class="input-group">
                <input type="number" class="form-control form-control-sm" id="goToPage" min="1"
                    max="{{ $paginator->lastPage() }}" value="{{ $paginator->currentPage() }}">
                <button class="btn btn-sm btn-primary" onclick="goToPage()">
                    <i class="mx-5 fas fa-chevron-right"></i>
                </button>
            </div>
        </div> --}}
    </nav>
@endif
