@extends('layouts.shop')

@section('title', 'Catalog')

@section('content')

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

        @if ($products->isEmpty())

            <div class="grid grid-cols-1 lg:grid-cols-[244px_minmax(0,1fr)]">

                {{-- ================================================= --}}
                {{-- SIDEBAR --}}
                {{-- ================================================= --}}

                <aside class="hidden lg:block border border-neutral-200 rounded-lg">

                    <form action="{{ route('shop.index') }}" method="GET">

                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <input type="hidden" name="color" value="{{ request('color') }}">
                        <input type="hidden" name="size" value="{{ request('size') }}">

                        {{-- SEARCH --}}
                        <div class="p-2">

                            <div class="relative">

                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-800" fill="none"
                                    stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="6.5" />
                                    <path stroke-linecap="round" d="m16 16 4 4" />
                                </svg>

                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                                    class="w-full h-[52px] pl-10 pr-3 border border-neutral-200 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:border-neutral-400 focus:ring-0">

                            </div>

                        </div>


                        {{-- PRODUCT TYPE --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Product Type
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="space-y-5">

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="product_type" value="all" @checked(request('product_type', 'all') === 'all') onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        All Products
                                    </span>

                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="product_type" value="featured"
                                        @checked(request('product_type') === 'featured') onchange="this.form.submit()"
                                        class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Featured Products
                                    </span>

                                </label>

                            </div>

                        </div>


                        {{-- AVAILABILITY --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Availability
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="space-y-5">

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="availability" value="all" @checked(request('availability', 'all') === 'all') onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        All
                                    </span>

                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="availability" value="in_stock"
                                        @checked(request('availability') === 'in_stock') onchange="this.form.submit()"
                                        class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        In Stock
                                    </span>

                                </label>

                            </div>

                        </div>


                        {{-- PRICE --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Price
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="space-y-5">

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="under_340" @checked(request('price') === 'under_340')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Under Rp 340,000
                                    </span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="340_410" @checked(request('price') === '340_410')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Rp 340,000 - Rp 410,000
                                    </span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="410_480" @checked(request('price') === '410_480')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Rp 410,000 - Rp 480,000
                                    </span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="480_plus" @checked(request('price') === '480_plus')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Rp 480,000 +
                                    </span>
                                </label>

                            </div>

                        </div>


                        {{-- COLOR --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Color
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="flex items-center gap-2">

                                <button type="button" onclick="this.form.color.value='black'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-neutral-900 border border-neutral-300"></button>

                                <button type="button" onclick="this.form.color.value='yellow'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-yellow-400"></button>

                                <button type="button" onclick="this.form.color.value='pink'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-pink-400"></button>

                                <button type="button" onclick="this.form.color.value='white'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-white border border-neutral-300"></button>

                                <button type="button" onclick="this.form.color.value='gray'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-neutral-400"></button>

                                <button type="button" onclick="this.form.color.value='blue'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-blue-400"></button>

                            </div>

                        </div>


                        {{-- SIZE --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Size
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m6 15 6-6 6 6
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    " />
                                </svg>

                            </div>

                            <div class="flex flex-wrap gap-2">

                                @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)

                                    <button type="button" onclick="this.form.size.value='{{ $size }}'; this.form.submit()"
                                        class="min-w-[40px] h-[30px] px-2 border border-neutral-200 text-[11px] font-bold hover:border-neutral-900 transition">
                                        {{ $size }}
                                    </button>

                                @endforeach

                            </div>

                        </div>

                    </form>

                </aside>

                <div class="p-12 text-center">
                    <p class="text-lg font-semibold text-neutral-900">
                        Produk tidak ditemukan
                    </p>

                    <p class="mt-1 text-sm text-neutral-500">
                        Coba kata kunci atau kategori lain.
                    </p>
                </div>
            </div>
        @else

            <div class="grid grid-cols-1 lg:grid-cols-[244px_minmax(0,1fr)]">

                {{-- ================================================= --}}
                {{-- SIDEBAR --}}
                {{-- ================================================= --}}

                <aside class="hidden lg:block border border-neutral-200 rounded-lg">

                    <form action="{{ route('shop.index') }}" method="GET">

                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <input type="hidden" name="color" value="{{ request('color') }}">
                        <input type="hidden" name="size" value="{{ request('size') }}">

                        {{-- SEARCH --}}
                        <div class="p-2">

                            <div class="relative">

                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-800" fill="none"
                                    stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="6.5" />
                                    <path stroke-linecap="round" d="m16 16 4 4" />
                                </svg>

                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                                    class="w-full h-[52px] pl-10 pr-3 border border-neutral-200 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:border-neutral-400 focus:ring-0">

                            </div>

                        </div>


                        {{-- PRODUCT TYPE --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Product Type
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="space-y-5">

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="product_type" value="all" @checked(request('product_type', 'all') === 'all') onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        All Products
                                    </span>

                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="product_type" value="featured"
                                        @checked(request('product_type') === 'featured') onchange="this.form.submit()"
                                        class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Featured Products
                                    </span>

                                </label>

                            </div>

                        </div>


                        {{-- AVAILABILITY --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Availability
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="space-y-5">

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="availability" value="all" @checked(request('availability', 'all') === 'all') onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        All
                                    </span>

                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">

                                    <input type="radio" name="availability" value="in_stock"
                                        @checked(request('availability') === 'in_stock') onchange="this.form.submit()"
                                        class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        In Stock
                                    </span>

                                </label>

                            </div>

                        </div>


                        {{-- PRICE --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Price
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="space-y-5">

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="under_340" @checked(request('price') === 'under_340')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Under Rp 340,000
                                    </span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="340_410" @checked(request('price') === '340_410')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Rp 340,000 - Rp 410,000
                                    </span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="410_480" @checked(request('price') === '410_480')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Rp 410,000 - Rp 480,000
                                    </span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price" value="480_plus" @checked(request('price') === '480_plus')
                                        onchange="this.form.submit()" class="w-4 h-4 accent-black">

                                    <span class="text-[12px] font-semibold">
                                        Rp 480,000 +
                                    </span>
                                </label>

                            </div>

                        </div>


                        {{-- COLOR --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Color
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
                                </svg>

                            </div>

                            <div class="flex items-center gap-2">

                                <button type="button" onclick="this.form.color.value='black'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-neutral-900 border border-neutral-300"></button>

                                <button type="button" onclick="this.form.color.value='yellow'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-yellow-400"></button>

                                <button type="button" onclick="this.form.color.value='pink'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-pink-400"></button>

                                <button type="button" onclick="this.form.color.value='white'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-white border border-neutral-300"></button>

                                <button type="button" onclick="this.form.color.value='gray'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-neutral-400"></button>

                                <button type="button" onclick="this.form.color.value='blue'; this.form.submit()"
                                    class="w-5 h-5 rounded-full bg-blue-400"></button>

                            </div>

                        </div>


                        {{-- SIZE --}}
                        <div class="border-t border-neutral-200 px-4 py-5">

                            <div class="flex items-center justify-between mb-5">

                                <h3 class="text-[12px] font-bold text-neutral-900">
                                    Size
                                </h3>

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m6 15 6-6 6 6
                                                                                                                                                                                                                                                                                                                                                                                                                                                        " />
                                </svg>

                            </div>

                            <div class="flex flex-wrap gap-2">

                                @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)

                                    <button type="button" onclick="this.form.size.value='{{ $size }}'; this.form.submit()"
                                        class="min-w-[40px] h-[30px] px-2 border border-neutral-200 text-[11px] font-bold hover:border-neutral-900 transition">
                                        {{ $size }}
                                    </button>

                                @endforeach

                            </div>

                        </div>

                    </form>

                </aside>


                {{-- ================================================= --}}
                {{-- PRODUCT AREA --}}
                {{-- ================================================= --}}

                <main class="min-w-0 lg:pl-5">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between mb-5">

                        <h1 class="text-xl font-normal text-neutral-900">

                            @if(request('category'))

                                {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Products' }}

                            @else

                                All Products

                            @endif

                        </h1>


                        {{-- SORT --}}
                        <form method="GET" action="{{ route('shop.index') }}">

                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif

                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            @if(request('product_type'))
                                <input type="hidden" name="product_type" value="{{ request('product_type') }}">
                            @endif

                            @if(request('availability'))
                                <input type="hidden" name="availability" value="{{ request('availability') }}">
                            @endif

                            @if(request('price'))
                                <input type="hidden" name="price" value="{{ request('price') }}">
                            @endif

                            @if(request('color'))
                                <input type="hidden" name="color" value="{{ request('color') }}">
                            @endif

                            @if(request('size'))
                                <input type="hidden" name="size" value="{{ request('size') }}">
                            @endif

                            <select name="sort" onchange="this.form.submit()"
                                class="h-[38px] pl-3 pr-8 border border-neutral-200 rounded-md bg-white text-[11px] font-bold text-neutral-900 focus:outline-none focus:ring-0 focus:border-neutral-400">

                                <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>
                                    Sort : Featured
                                </option>

                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>
                                    Newest
                                </option>

                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>
                                    Price: Low to High
                                </option>

                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>
                                    Price: High to Low
                                </option>

                            </select>

                        </form>

                    </div>


                    {{-- PRODUCTS --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-12 rounded-lg">

                        @foreach ($products as $product)

                                <a href="{{ route('shop.show', $product) }}"
                                    class="group block hover:bg-neutral-500/5 transition-colors duration-300"">

                                                                                                                                                                                                                                                                                {{-- PRODUCT IMAGE --}}
                                                                                                                                                                                                                                                                                <div class="
                                    aspect-square bg-paper overflow-hidden">
                                    @if ($product->stock > 0 && $product->stock <= 5)
                                        <div class="mb-2">

                                            <span class="inline-block bg-neutral-400 text-white px-1.5 py-1 text-[8px] font-bold uppercase">
                                                Low Stock
                                            </span>

                                        </div>
                                    @elseif ($product->stock <= 0)
                                        <div class="mb-2">

                                            <span class="inline-block bg-neutral-400 text-white px-1.5 py-1 text-[8px] font-bold uppercase">
                                                Sold Out
                                            </span>

                                        </div>
                                    @endif
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-contain group-hover:scale-[1.03] transition-transform duration-500"
                                        loading="lazy">

                            </div>


                            {{-- PRODUCT INFO --}}
                            <div class="mb-1">
                                <h3 class="text-[12px] font-bold uppercase text-neutral-900 line-clamp-2 p-2">
                                    {{ $product->name }}
                                </h3>
                                <span class="text-[12px] font-bold text-neutral-900 p-2">
                                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                </span>
                            </div>

                            </a>

                        @endforeach

            </div>


            {{-- PAGINATION --}}
            <div class="mt-14">
                {{ $products->links() }}
            </div>

            </main>

            </div>

        @endif

    </section>

@endsection