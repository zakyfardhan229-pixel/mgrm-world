@extends('layouts.shop')

@section('title', 'Pesanan Saya')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-900">Pesanan Saya</h1>

        @if ($orders->isEmpty())
            <div class="mt-6 rounded-3xl bg-white border border-neutral-200 shadow-card p-12 text-center">
                <p class="text-lg font-semibold text-neutral-900">Belum ada pesanan</p>
                <p class="mt-1 text-sm text-neutral-500">Mulai belanja untuk membuat pesanan pertamamu.</p>
                <a href="{{ route('shop.index') }}"
                    class="mt-6 inline-flex items-center rounded-full bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Lihat
                    Katalog</a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}"
                        class="block bg-white rounded-lg border border-neutral-200 shadow-card hover:shadow-hover transition p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <p class="font-extrabold text-ink">{{ $order->order_number }}</p>
                                    <p class="text-sm text-neutral-500 mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }}
                                        · {{ $order->items_count }} item</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-lg px-3 py-1.5 text-xs font-bold
                                                        @if ($order->status->value === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                                        @elseif ($order->status->value === 'cancelled') bg-red-50 text-red-700 border border-red-200
                                                        @elseif ($order->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                                        @else bg-neutral-100 text-neutral-700 @endif">
                                        {{ $order->status->label() }}
                                    </span>
                                    <span class="font-extrabold text-ink">Rp
                                        {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $orders->links() }}
            </div>
        @endif
    </section>
@endsection