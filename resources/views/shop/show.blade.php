@extends('layouts.shop')

@section('title', $product->name)

@section('content')

    <section x-data="{
                                                                    quantity: 1,
                                                                    maxQuantity: {{ max((int) $product->stock, 1) }},
                                                                    selectedImage: '{{ $product->image_url }}',

                                                                    increase() {
                                                                        if (this.quantity < this.maxQuantity) {
                                                                            this.quantity++
                                                                        }
                                                                    },

                                                                    decrease() {
                                                                        if (this.quantity > 1) {
                                                                            this.quantity--
                                                                        }
                                                                    }
                                                                }"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12">

            {{-- ===================================================== --}}
            {{-- LEFT : PRODUCT IMAGE --}}
            {{-- ===================================================== --}}

            <div>

                {{-- MAIN IMAGE --}}
                <div class="w-full aspect-square bg-paper overflow-hidden">

                    <img :src="selectedImage" src="{{ $product->image_url }}" alt="{{ $product->name }}"
                        class="w-full h-full object-contain">

                </div>


                {{-- THUMBNAILS --}}
                <div class="mt-3 flex items-center gap-3 overflow-x-auto">

                    <button type="button" @click="selectedImage = '{{ $product->image_url }}'"
                        class="w-20 h-20 shrink-0 border border-neutral-900 bg-paper overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain">
                    </button>

                </div>


                {{-- YOU MIGHT ALSO LIKE --}}
                @if ($relatedProducts->isNotEmpty())

                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- RIGHT : PRODUCT INFORMATION --}}
            {{-- ===================================================== --}}

            <div class="pt-0 lg:pt-1">

                {{-- STOCK --}}
                @if ($product->stock > 0)

                    <span class="inline-flex bg-neutral-800 text-white px-2 py-1 text-[10px] font-bold">
                        In Stock
                    </span>

                @else

                    <span class="inline-flex bg-neutral-300 text-white px-2 py-1 text-[10px] font-bold">
                        Out of Stock
                    </span>

                @endif


                {{-- PRODUCT NAME + FAVORITE --}}
                <div class="mt-2 flex items-start justify-between gap-5">

                    <h1 class="text-xl sm:text-2xl font-normal tracking-tight text-neutral-900 leading-tight">
                        {{ $product->name }}
                    </h1>

                    {{-- HEART --}}
                    <button type="button" class="shrink-0 mt-1 text-neutral-700 hover:text-black transition"
                        aria-label="Add to wishlist">

                        <!-- <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z" />
                                </svg> -->

                    </button>

                </div>


                {{-- PRICE --}}
                <div class="mt-4">

                    <span class="text-sm font-bold text-neutral-900">
                        Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                    </span>

                </div>


                <!-- {{-- QR CODE --}}
                <div class="mt-5 border border-neutral-200 rounded-lg">

                    <div class="px-3 py-3">

                        <h3 class="text-[12px] font-bold text-neutral-900">
                            QR Code
                        </h3>

                        <div class="mt-2 flex items-center gap-4">

                            <img src="{{ route('shop.qr', $product) }}" alt="QR Code Produk" loading="lazy"
                                class="h-28 w-28 object-contain">

                            <p class="text-[10px] text-neutral-500">
                                Scan untuk membuka halaman produk.
                            </p>

                        </div>

                    </div>

                </div> -->
                {{-- QUANTITY INFORMATION --}}
                <div class="mt-5 border border-neutral-200 rounded-lg">

                    <div class="px-3 py-3">

                        <h3 class="text-[12px] font-bold text-neutral-900">
                            Quantity Information
                        </h3>

                        <div class="mt-2 flex items-center justify-between">

                            <span class="text-[11px] text-neutral-500">
                                Maximum Quantity:
                            </span>

                            <span class="text-[12px] font-bold text-neutral-900">
                                {{ $product->stock }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- COLOR & SIZE --}}
                @if ($product->color || $product->size)

                    <div class="mt-5 border border-neutral-200 rounded-lg">

                        <div class="px-3 py-3">

                            <h3 class="text-[12px] font-bold text-neutral-900">
                                Detail Produk
                            </h3>

                            <div class="mt-2 flex items-center justify-between">

                                <span class="text-[11px] text-neutral-500">
                                    Warna:
                                </span>

                                <span class="text-[12px] font-bold text-neutral-900">
                                    {{ $product->color ? ucfirst($product->color) : '-' }}
                                </span>

                            </div>

                            <div class="mt-2 flex items-center justify-between">

                                <span class="text-[11px] text-neutral-500">
                                    Ukuran:
                                </span>

                                <span class="text-[12px] font-bold text-neutral-900">
                                    {{ $product->size ?: '-' }}
                                </span>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- QUANTITY --}}
                <div class="mt-6">

                    <div class="inline-flex items-center border border-neutral-200 rounded-md overflow-hidden">

                        <button type="button" @click="decrease()"
                            class="w-9 h-9 flex items-center justify-center text-neutral-500 hover:bg-neutral-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M5 12h14" />
                            </svg>
                        </button>


                        <div class="w-12 h-9 flex items-center justify-center border-x border-neutral-200">

                            <span x-text="quantity" class="text-sm font-semibold text-neutral-900"></span>

                        </div>


                        <button type="button" @click="increase()"
                            class="w-9 h-9 flex items-center justify-center text-neutral-500 hover:bg-neutral-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                            </svg>
                        </button>

                    </div>

                </div>


                {{-- ACTIONS --}}
                @auth

                    @if ($product->stock > 0)

                        <form action="{{ route('cart.store') }}" method="POST" class="mt-7">

                            @csrf

                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <input type="hidden" name="quantity" :value="quantity">


                            {{-- ADD TO CART --}}
                            <button type="submit"
                                class="w-full h-[36px] border border-neutral-900 rounded-xl bg-white text-neutral-900 text-xs font-bold hover:bg-neutral-100 transition">
                                Add to Cart
                            </button>


                            {{-- BUY NOW --}}
                            <button type="submit" name="buy_now" value="1"
                                class="mt-2 w-full h-[36px] rounded-xl bg-neutral-900 text-white text-xs font-bold hover:bg-black transition">
                                Buy It Now
                            </button>

                        </form>

                    @else

                        <div class="mt-7">

                            <div
                                class="w-full rounded-xl bg-neutral-100 px-4 py-3 text-center text-sm font-semibold text-neutral-500">
                                Produk ini sedang tidak tersedia.
                            </div>

                        </div>

                    @endif

                @else

                    <div class="mt-7">

                        <a href="{{ route('login') }}"
                            class="block w-full h-[36px] rounded-xl bg-neutral-900 text-white text-xs font-bold flex items-center justify-center hover:bg-black transition">
                            Masuk untuk Membeli
                        </a>

                        <a href="{{ route('register') }}"
                            class="mt-2 block w-full h-[36px] border border-neutral-900 rounded-xl bg-white text-neutral-900 text-xs font-bold flex items-center justify-center hover:bg-neutral-100 transition">
                            Buat Akun
                        </a>

                    </div>

                @endauth


                {{-- DESCRIPTION --}}
                <div class="mt-8">

                    <p class="text-[11px] font-bold uppercase text-neutral-900">
                        {{ $product->description ?: 'MAD DESIGN.' }}
                    </p>

                </div>


                {{-- DELIVERY --}}
                <div class="mt-5 border border-neutral-200 rounded-lg">

                    <div class="px-3 py-3">

                        <h3 class="text-[12px] font-bold text-neutral-900">
                            Delivery
                        </h3>


                        <div class="mt-3 flex items-start justify-between gap-5">

                            <div>

                                <p class="text-[11px] font-bold text-neutral-900">
                                    Deliver to:
                                </p>

                                <p class="mt-1 text-[11px] font-bold text-neutral-900">
                                    Weight:
                                </p>

                                <p class="mt-4 text-[11px] font-semibold text-neutral-500 leading-4">
                                    Shipped within 5 days.<br>
                                    (Upon confirmation of payment)
                                </p>

                            </div>


                            <div class="text-right">

                                <button type="button"
                                    class="flex items-center gap-1 ml-auto text-[11px] font-bold text-neutral-900">
                                    Pick Area

                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                    </svg>

                                </button>

                                <p class="mt-1 text-[11px] font-bold text-neutral-900">
                                    500g
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- RELATED PRODUCTS --}}
    {{-- ========================================================= --}}

    @if ($relatedProducts->isNotEmpty())

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">

            <h2 class="text-sm font-bold text-neutral-900">
                You Might Also Like
            </h2>


            <div class="mt-5 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">

                @foreach ($relatedProducts as $related)

                    <a href="{{ route('shop.show', $related) }}" class="group block">

                        <div class="aspect-square bg-paper overflow-hidden">

                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}"
                                class="w-full h-full object-contain group-hover:scale-[1.03] transition-transform duration-500"
                                loading="lazy">

                        </div>


                        <div class="pt-3">

                            <h3 class="text-[11px] font-bold uppercase leading-5 text-neutral-900 line-clamp-2">
                                {{ $related->name }}
                            </h3>

                            <p class="mt-1 text-[12px] font-bold text-neutral-900">
                                Rp {{ number_format((float) $related->price, 0, ',', '.') }}
                            </p>

                        </div>

                    </a>

                @endforeach

            </div>

        </section>

    @endif

@endsection