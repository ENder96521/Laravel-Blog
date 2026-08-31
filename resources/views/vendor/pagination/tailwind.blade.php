@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4 font-mono text-sm">
        @if ($paginator->onFirstPage())
            <span class="text-neutral-400">← {{ __('pagination.previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="hover:text-accent">← {{ __('pagination.previous') }}</a>
        @endif

        <div class="flex items-center gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-neutral-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="flex size-8 items-center justify-center border border-accent text-accent">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex size-8 items-center justify-center hover:text-accent" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="hover:text-accent">{{ __('pagination.next') }} →</a>
        @else
            <span class="text-neutral-400">{{ __('pagination.next') }} →</span>
        @endif
    </nav>
@endif
