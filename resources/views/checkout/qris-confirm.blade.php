@extends('layouts.shop')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">
        <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-6 sm:p-8">
            <h1 class="text-2xl font-extrabold text-neutral-900">Konfirmasi Pembayaran QRIS</h1>
            <p class="mt-1 text-sm text-neutral-500">Silakan periksa detail pesanan berikut sebelum menyelesaikan pembayaran.</p>

            <div class="mt-6 bg-white rounded-lg border border-neutral-200 p-5">
                <h2 class="font-extrabold text-neutral-900">Detail Pesanan</h2>
                <div class="mt-4 divide-y divide-neutral-100">
                    @foreach ($lines as $line)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-neutral-900 line-clamp-1">{{ $line['product']->name }}</p>
                                <p class="text-xs text-neutral-400 mt-0.5">Rp
                                    {{ number_format((float) $line['product']->price, 0, ',', '.') }} × {{ $line['quantity'] }}
                                </p>
                            </div>
                            <p class="font-extrabold text-ink shrink-0">Rp
                                {{ number_format((int) round($line['product']->price * 100) * $line['quantity'] / 100, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-neutral-100 pt-4 flex items-center justify-between">
                    <span class="font-bold text-neutral-900">Total</span>
                    <span class="font-extrabold text-ink text-lg">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-5 bg-white rounded-lg border border-neutral-200 p-5">
                <h2 class="font-extrabold text-neutral-900">Data Pengiriman</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-neutral-500">Penerima</dt>
                        <dd class="font-semibold text-neutral-900 text-right">{{ $payload['customer_name'] }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-neutral-500">Telepon</dt>
                        <dd class="font-semibold text-neutral-900 text-right">{{ $payload['phone'] }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-neutral-500">Alamat</dt>
                        <dd class="font-semibold text-neutral-900 text-right max-w-[70%]">{{ $payload['address'] }}</dd>
                    </div>
                    @if (!empty($payload['notes']))
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-neutral-500">Catatan</dt>
                            <dd class="font-semibold text-neutral-900 text-right max-w-[70%]">{{ $payload['notes'] }}</dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-neutral-500">Metode Bayar</dt>
                        <dd class="font-semibold text-neutral-900">QRIS</dd>
                    </div>
                </dl>
            </div>

            <form action="{{ route('checkout.qris.process', $token) }}" method="POST"
                class="mt-8 flex flex-col sm:flex-row gap-3">
                @csrf
                <button type="submit"
                    class="flex-1 rounded-full bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                    Konfirmasi & Selesaikan Pembayaran
                </button>
            </form>

            <p class="mt-3 text-center text-xs text-neutral-400">
                Dengan mengonfirmasi, pesanan akan dibuat dan pembayaran dicatat sebagai berhasil.
            </p>
        </div>
    </section>
@endsection
