@extends('layouts.shop')

@section('title', 'Arsip')

@section('content')

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

        <div class="mb-8">

            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-4">
                Arsip
            </p>

            <h1 class="text-2xl md:text-3xl font-bold text-neutral-900">
                Produk Tidak Tersedia
            </h1>

            <p class="mt-2 text-sm text-neutral-500">
                Berikut adalah koleksi produk yang memang tidak untuk dijual lagi.
            </p>

        </div>

        @if ($products->isEmpty())

            <div class="border border-neutral-200 p-12 text-center">
                <p class="text-lg font-semibold text-neutral-900">
                    Tidak ada item di arsip
                </p>

                <p class="mt-1 text-sm text-neutral-500">
                    Produk yang dinonaktifkan akan muncul di sini.
                </p>
            </div>

        @else

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach ($products as $product)

                    <div class="group block opacity-60 saturate-50">

                        <div class="aspect-square bg-white overflow-hidden">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="w-full h-full object-contain">
                        </div>

                        <div class="pt-3">
                            <h3 class="text-[11px] font-bold uppercase leading-5 text-neutral-900 line-clamp-2">
                                {{ $product->name }}
                            </h3>
                            <div class="mt-1">
                                <span class="text-[12px] font-bold text-neutral-500">
                                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-neutral-400">
                                Tidak tersedia
                            </p>
                        </div>

                    </div>

                @endforeach

            </div>

            <div class="mt-14">
                {{ $products->links() }}
            </div>

        @endif

    </section>

@endsection