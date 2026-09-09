@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="{{ __('blue.admin.pagination') }}">
        <ul class="pagination__list" role="list">
            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pagination__link pagination__link--disabled" aria-hidden="true">←</span>
                @else
                    <a class="pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('blue.admin.pagination_previous') }}">←</a>
                @endif
            </li>

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination__link pagination__link--disabled" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            <a class="pagination__link {{ $page === $paginator->currentPage() ? 'pagination__link--active' : '' }}"
                               href="{{ $url }}" @if($page === $paginator->currentPage()) aria-current="page" @endif>
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a class="pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('blue.admin.pagination_next') }}">→</a>
                @else
                    <span class="pagination__link pagination__link--disabled" aria-hidden="true">→</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
