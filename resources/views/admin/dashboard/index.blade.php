@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <p class="text-sm font-semibold text-neutral-500">Pendapatan</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink">Rp {{ number_format((float) $revenue, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-neutral-400">Kecuali pesanan dibatalkan</p>
        </div>
        <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <p class="text-sm font-semibold text-neutral-500">Total Pesanan</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink">{{ number_format($totalOrders, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-neutral-400">Pesanan berhasil</p>
        </div>
        <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <p class="text-sm font-semibold text-neutral-500">Produk</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink">{{ number_format($productsCount, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-neutral-400">Total di katalog</p>
        </div>
        <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <p class="text-sm font-semibold text-neutral-500">Kategori</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink">{{ number_format($categoriesCount, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-neutral-400">Pengelompokan produk</p>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="font-extrabold text-neutral-900">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-ink hover:text-neutral-500 transition">Lihat Semua</a>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[600px] text-sm">
                <thead>
                    <tr class="text-left text-xs font-bold uppercase tracking-wider text-neutral-400 border-b border-neutral-100">
                        <th class="pb-3 pr-4">No. Pesanan</th>
                        <th class="pb-3 pr-4">Customer</th>
                        <th class="pb-3 pr-4">Total</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td class="py-3 pr-4"><a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-ink hover:underline">{{ $order->order_number }}</a></td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $order->customer_name }}</td>
                            <td class="py-3 pr-4 font-semibold text-neutral-900">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold
                                    @if ($order->status->value === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif ($order->status->value === 'cancelled') bg-red-50 text-red-700 border border-red-200
                                    @elseif ($order->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-neutral-100 text-neutral-700 @endif">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 text-neutral-500 whitespace-nowrap">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection