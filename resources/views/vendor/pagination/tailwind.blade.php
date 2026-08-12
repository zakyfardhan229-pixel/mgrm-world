@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Mobile Pagination --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">

            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center rounded-md border border-black bg-black px-4 py-2 text-sm font-medium leading-5 text-white/50 cursor-not-allowed">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center rounded-md border border-black bg-black px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 active:bg-white active:text-black">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center rounded-md border border-black bg-black px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 active:bg-white active:text-black">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="inline-flex items-center rounded-md border border-black bg-black px-4 py-2 text-sm font-medium leading-5 text-white/50 cursor-not-allowed">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        {{-- Desktop Pagination --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm leading-5 text-black">
                    {!! __('Showing') !!}

                    @if ($paginator->firstItem())
                        <span class="font-medium text-black">
                            {{ $paginator->firstItem() }}
                        </span>

                        {!! __('to') !!}

                        <span class="font-medium text-black">
                            {{ $paginator->lastItem() }}
                        </span>
                    @else
                        {{ $paginator->count() }}
                    @endif

                    {!! __('of') !!}

                    <span class="font-medium text-black">
                        {{ $paginator->total() }}
                    </span>

                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex rounded-md rtl:flex-row-reverse">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="inline-flex items-center rounded-l-md border border-black bg-black px-2 py-2 text-sm font-medium leading-5 text-white/50 cursor-not-allowed"
                                aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="inline-flex items-center rounded-l-md border border-black bg-black px-2 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 active:bg-white active:text-black"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)

                        {{-- Three Dots --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="inline-flex items-center border border-black bg-black px-4 py-2 text-sm font-medium leading-5 text-white cursor-default">
                                    {{ $element }}
                                </span>
                            </span>
                        @endif

                        {{-- Page Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)

                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="inline-flex items-center border border-black bg-black px-4 py-2 text-sm font-semibold leading-5 text-white cursor-default">
                                            {{ $page }}
                                        </span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex items-center border border-black bg-white px-4 py-2 text-sm font-medium leading-5 text-black transition-colors duration-150 hover:bg-black hover:text-white focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 active:bg-white active:text-black"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif

                            @endforeach
                        @endif

                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="inline-flex items-center rounded-r-md border border-black bg-black px-2 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 hover:bg-white hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 active:bg-white active:text-black"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010 1.414L10.586 10 7.293 6.707a1 1 0 011.414 1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="inline-flex items-center rounded-r-md border border-black bg-black px-2 py-2 text-sm font-medium leading-5 text-white/50 cursor-not-allowed"
                                aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010 1.414L10.586 10 7.293 6.707a1 1 0 011.414 1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif

                </span>
            </div>

        </div>
    </nav>

@endif