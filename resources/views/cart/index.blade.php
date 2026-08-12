@extends('layouts.shop')

@section('title', 'Keranjang Belanja')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <h1 class="text-2xl sm:text-[25px] font-extrabold text-neutral-900">Keranjang Belanja</h1>

        @if ($cartItems->isEmpty())
            <div class="mt-6 rounded-lg bg-white border border-neutral-200 shadow-card p-12 text-center">
                <p class="text-lg font-semibold text-neutral-900">Keranjang belanja masih kosong</p>
                <p class="mt-1 text-sm text-neutral-500">Yuk pilih produk favoritmu di katalog.</p>
                <a href="{{ route('shop.index') }}"
                    class="mt-6 inline-flex items-center rounded-lg bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                    Lihat Katalog
                </a>
            </div>
        @else
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($cartItems as $item)
                        <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-4 flex gap-4 items-center">
                            <a href="{{ route('shop.show', $item->product) }}"
                                class="shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-lg bg-neutral-100 overflow-hidden">
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                    class="w-full h-full object-cover">
                            </a>

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('shop.show', $item->product) }}"
                                    class="font-semibold text-neutral-900 line-clamp-1 text-sm md:text-base hover:text-primary transition-colors">
                                    {{ $item->product->name }}
                                </a>


                                <p class="mt-0.5 md:mt-1 text-xs md:text-sm font-medium text-neutral-600">
                                    Rp {{ number_format((float) $item->product->price, 0, ',', '.') }}
                                </p>

                                <p class="text-[11px] md:text-xs text-neutral-400 mt-0.5">
                                    Stok tersedia: {{ $item->product->stock }}
                                </p>
                            </div>


                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2"
                                data-validate>
                                @csrf
                                @method('PATCH')
                                <div class="flex items-center rounded-lg bg-neutral-100 p-1">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                        max="{{ max($item->product->stock, 1) }}" required data-required data-number data-min="1"
                                        data-max="{{ max($item->product->stock, 1) }}" data-label="Jumlah"
                                        class="w-14 border-0 bg-transparent text-center text-sm font-semibold outline-none ring-0 shadow-none focus:border-0 focus:outline-none focus:ring-0 focus:shadow-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                </div>
                                <button type=" submit"
                                    class="rounded-lg bg-neutral-100 px-3 py-2 text-xs font-bold text-neutral-700 hover:bg-neutral-200 transition">Simpan</button>
                            </form>

                            <div class="text-right shrink-0">
                                <p class="font-extrabold text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                                <form action="{{ route('cart.destroy', $item) }}" method="POST" class="mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs font-semibold text-red-600 hover:text-red-800 transition">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-6 sticky top-20">
                    <h2 class="font-extrabold text-neutral-900">Ringkasan Belanja</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-neutral-500">Total Item</dt>
                            <dd class="font-semibold text-neutral-900">{{ $cartItems->sum('quantity') }} produk</dd>
                        </div>
                        <div class="border-t border-neutral-100 pt-3 flex items-center justify-between">
                            <dt class="font-bold text-neutral-900">Total Belanja</dt>
                            <dd class="font-extrabold text-ink text-lg">Rp
                                {{ number_format((float) $cartItems->sum(fn($item) => (float) $item->subtotal), 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                    <a href="{{ route('checkout.index') }}"
                        class="mt-6 flex items-center justify-center rounded-lg bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                        Lanjut ke Checkout
                    </a>
                    <a href="{{ route('shop.index') }}"
                        class="mt-3 flex items-center justify-center rounded-lg bg-white border border-neutral-300 px-6 py-3 text-sm font-bold text-neutral-700 hover:bg-black hover:text-white transition">
                        Belanja Lagi
                    </a>
                </div>
            </div>
        @endif
    </section>
@endsection