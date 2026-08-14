@extends('layouts.shop')

@section('title', $product->name)

@section('content')

    @php
        // Daftar semua gambar produk (legacy utama + galeri), di-dedupe berdasarkan path.
        $galleryUrls = collect()
            ->push($product->image)
            ->merge($product->images->pluck('image_path'))
            ->filter()
            ->unique()
            ->map(fn ($path) => Storage::disk('public')->url($path))
            ->values()
            ->all();

        // Tidak ada gambar sama sekali -> fallback ke placeholder / image_url.
        if (empty($galleryUrls)) {
            $galleryUrls = [$product->image_url];
        }

        // Indeks default = gambar utama yang sedang ditampilkan.
        $activeIndex = array_search($product->image_url, $galleryUrls, true);
        $activeIndex = $activeIndex === false ? 0 : $activeIndex;
    @endphp

    <section x-data="{
                        quantity: 1,
                        maxQuantity: {{ max((int) $product->stock, 1) }},
                        images: @js($galleryUrls),
                        activeIndex: {{ $activeIndex }},
                        touchStartX: null,
                        lightboxOpen: false,

                        get selectedImage() {
                            return this.images[this.activeIndex];
                        },

                        increase() {
                            if (this.quantity < this.maxQuantity) {
                                this.quantity++
                            }
                        },

                        decrease() {
                            if (this.quantity > 1) {
                                this.quantity--
                            }
                        },

                        next() {
                            this.activeIndex = (this.activeIndex + 1) % this.images.length;
                        },

                        prev() {
                            this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
                        },

                        select(i) {
                            this.activeIndex = i;
                        },

                        openLightbox() {
                            this.lightboxOpen = true;
                        },

                        closeLightbox() {
                            this.lightboxOpen = false;
                        }
                    }"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12">

            {{-- ===================================================== --}}
            {{-- LEFT : PRODUCT IMAGE --}}
            {{-- ===================================================== --}}

            <div>

                {{-- MAIN IMAGE --}}
                <div class="relative w-full aspect-square bg-paper overflow-hidden group cursor-zoom-in"
                    @click="openLightbox()"
                    @touchstart.passive="touchStartX = $event.changedTouches[0].clientX"
                    @touchend.passive="if (touchStartX !== null) {
                                            const dx = $event.changedTouches[0].clientX - touchStartX;
                                            if (Math.abs(dx) > 50) { dx < 0 ? next() : prev(); }
                                            touchStartX = null;
                                        }">

                    <img :src="selectedImage" src="{{ $product->image_url }}" alt="{{ $product->name }}"
                        class="w-full h-full object-contain">

                    {{-- PREV --}}
                    <button type="button" @click.stop="prev()" x-show="images.length > 1"
                        class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-11 h-11 sm:w-12 sm:h-12
                               flex items-center justify-center rounded-full bg-white/90 shadow-card
                               text-neutral-900 hover:bg-white transition touch-manipulation"
                        aria-label="Gambar sebelumnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    {{-- NEXT --}}
                    <button type="button" @click.stop="next()" x-show="images.length > 1"
                        class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-11 h-11 sm:w-12 sm:h-12
                               flex items-center justify-center rounded-full bg-white/90 shadow-card
                               text-neutral-900 hover:bg-white transition touch-manipulation"
                        aria-label="Gambar berikutnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- ZOOM HINT --}}
                    <span class="absolute bottom-3 left-3 z-10 hidden sm:flex items-center gap-1.5 rounded-full
                                 bg-white/80 text-neutral-700 px-2.5 py-1.5 text-[11px] font-semibold shadow-card
                                 opacity-0 group-hover:opacity-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16 11a5 5 0 11-10 0 5 5 0 0110 0zM11 8v6M8 11h6" />
                        </svg>
                        Perbesar
                    </span>

                    {{-- COUNTER --}}
                    <span x-show="images.length > 1"
                        class="absolute bottom-3 right-3 z-10 rounded-full bg-black/60 text-white text-[10px]
                               font-semibold px-2.5 py-1"
                        x-text="(activeIndex + 1) + ' / ' + images.length"></span>

                </div>


                {{-- THUMBNAILS --}}
                <div class="mt-3 flex items-center gap-3 overflow-x-auto overscroll-x-contain snap-x
                            [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    <template x-for="(img, i) in images" :key="i">
                        <button type="button" @click="select(i)" :class="i === activeIndex ? 'border-neutral-900' : 'border-neutral-200'"
                            class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 snap-start rounded-lg border border-neutral-200
                                   bg-paper overflow-hidden touch-manipulation transition">
                            <img :src="img" :alt="'{{ $product->name }} ' + (i + 1)"
                                class="w-full h-full object-contain">
                        </button>
                    </template>
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

        {{-- ===================================================== --}}
        {{-- LIGHTBOX --}}
        {{-- ===================================================== --}}
        <div x-show="lightboxOpen"
            x-cloak
            x-transition.opacity.duration.200ms
            x-init="$watch('lightboxOpen', v => document.body.classList.toggle('overflow-y-hidden', v))"
            @keydown.escape.window="lightboxOpen = false"
            class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center"
            @click="lightboxOpen = false">

            {{-- GAMBAR BESAR --}}
            <img :src="selectedImage" alt="{{ $product->name }}"
                class="max-h-[88vh] max-w-[92vw] object-contain" @click.stop>

            {{-- PREV --}}
            <button type="button" @click.stop="prev()" x-show="images.length > 1"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 flex items-center justify-center
                       rounded-full bg-white/10 text-white hover:bg-white/20 transition touch-manipulation"
                aria-label="Gambar sebelumnya">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- NEXT --}}
            <button type="button" @click.stop="next()" x-show="images.length > 1"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 flex items-center justify-center
                       rounded-full bg-white/10 text-white hover:bg-white/20 transition touch-manipulation"
                aria-label="Gambar berikutnya">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- CLOSE --}}
            <button type="button" @click.stop="lightboxOpen = false"
                class="absolute top-4 right-4 z-10 w-12 h-12 flex items-center justify-center rounded-full
                       bg-white/10 text-white hover:bg-white/20 transition touch-manipulation"
                aria-label="Tutup">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>

            {{-- COUNTER --}}
            <span x-show="images.length > 1" x-text="(activeIndex + 1) + ' / ' + images.length"
                class="absolute top-4 left-4 z-10 rounded-full bg-white/10 text-white text-xs font-semibold px-3 py-1.5"></span>
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