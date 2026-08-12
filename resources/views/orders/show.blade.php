@extends('layouts.shop')

@section('title', 'Detail Pesanan')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-neutral-900">Pesanan {{ $order->order_number }}</h1>
                <p class="text-sm text-neutral-500 mt-1">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <span class="self-start rounded-full px-4 py-2 text-xs font-bold
                                    @if ($order->status->value === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif ($order->status->value === 'cancelled') bg-red-50 text-red-700 border border-red-200
                                    @elseif ($order->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-neutral-100 text-neutral-700 @endif">
                {{ $order->status->label() }}
            </span>
        </div>

        @if ($order->status->value === 'pending' && $order->payment_method->value === 'transfer')
            <div class="mt-6 rounded-2xl bg-ink text-white shadow-card p-5 sm:p-6">
                <h2 class="font-extrabold">Info Pembayaran</h2>
                <p class="mt-2 text-sm text-neutral-300">Transfer ke rekening berikut, lalu tunggu konfirmasi admin:</p>
                <p class="mt-3 text-lg font-extrabold">Bank BCA — 1234567890</p>
                <p class="text-sm text-neutral-300">a.n. MGRM World</p>
                <p class="mt-2 text-sm text-neutral-300">Total yang harus dibayar: <span class="font-extrabold text-white">Rp
                        {{ number_format((float) $order->total, 0, ',', '.') }}</span></p>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 bg-white rounded-lg border border-neutral-200 shadow-card p-6">
                <h2 class="font-extrabold text-neutral-900">Detail Produk</h2>
                <div class="mt-4 divide-y divide-neutral-100">
                    @foreach ($order->items as $item)
                        <div class="py-4 flex items-center gap-4">
                            <div class="shrink-0 w-16 h-16 rounded-xl bg-neutral-100 overflow-hidden">
                                <img src="{{ $item->product?->image_url }}" alt="{{ $item->product_name }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-neutral-900 line-clamp-1">{{ $item->product_name }}</p>
                                <p class="text-xs text-neutral-400 mt-0.5">Rp
                                    {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}
                                </p>
                            </div>
                            <p class="font-extrabold text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-6">
                    <h2 class="font-extrabold text-neutral-900">Ringkasan</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-neutral-500">Metode Bayar</dt>
                            <dd class="font-semibold text-neutral-900">{{ $order->payment_method->label() }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-neutral-500">Penerima</dt>
                            <dd class="font-semibold text-neutral-900">{{ $order->customer_name }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-neutral-500">Telepon</dt>
                            <dd class="font-semibold text-neutral-900">{{ $order->phone }}</dd>
                        </div>
                        <div class="border-t border-neutral-100 pt-3 flex items-center justify-between">
                            <dt class="font-bold text-neutral-900">Total</dt>
                            <dd class="font-extrabold text-ink text-lg">Rp
                                {{ number_format((float) $order->total, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-6">
                    <h2 class="font-extrabold text-neutral-900">Alamat Pengiriman</h2>
                    <p class="mt-3 text-sm text-neutral-600 leading-relaxed">{{ $order->address }}</p>
                    @if ($order->notes)
                        <p class="mt-3 text-sm text-neutral-500">Catatan: {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection