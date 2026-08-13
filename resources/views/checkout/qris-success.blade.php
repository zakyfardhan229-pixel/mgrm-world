@extends('layouts.shop')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">
        <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-6 sm:p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="mt-4 text-2xl font-extrabold text-neutral-900">Pembayaran Berhasil</h1>
            <p class="mt-1 text-sm text-neutral-500">
                Pesanan <span class="font-semibold text-neutral-900">{{ $order->order_number }}</span> berhasil dibuat.
            </p>

            <div class="mt-6 text-left bg-neutral-50 rounded-xl border border-neutral-200 p-5">
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-neutral-500">No. Pesanan</dt>
                        <dd class="font-semibold text-neutral-900">{{ $order->order_number }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-neutral-500">Status</dt>
                        <dd class="font-semibold text-emerald-600">{{ $order->status->label() }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-neutral-500">Metode Bayar</dt>
                        <dd class="font-semibold text-neutral-900">{{ $order->payment_method->label() }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-neutral-500">Total</dt>
                        <dd class="font-extrabold text-ink">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6">
                <a href="{{ route('shop.index') }}"
                    class="inline-flex items-center rounded-full bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                    Kembali ke Toko
                </a>
            </div>
        </div>
    </section>
@endsection
