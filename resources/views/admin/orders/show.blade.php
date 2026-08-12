@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('page', 'Detail Pesanan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-extrabold text-neutral-900">{{ $order->order_number }}</h2>
            <p class="text-sm text-neutral-500 mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
        </div>
         <div class="flex items-center gap-3">
            <span class="rounded-full px-4 py-2 text-xs font-bold
                @if ($order->status->value === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                @elseif ($order->status->value === 'cancelled') bg-red-50 text-red-700 border border-red-200
                @elseif ($order->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                @else bg-neutral-100 text-neutral-700 @endif">
                {{ $order->status->label() }}
            </span>
            <a href="{{ route('admin.orders.index') }}" class="rounded-full bg-white border border-neutral-300 px-4 py-2 text-xs font-bold text-neutral-700 hover:bg-neutral-100 transition">Kembali</a>
        </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        <div class="xl:col-span-2 bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <h3 class="font-extrabold text-neutral-900">Item Pesanan</h3>
            <div class="mt-4 divide-y divide-neutral-100">
                @foreach ($order->items as $item)
                    <div class="py-4 flex items-center gap-4">
                        <div class="shrink-0 w-14 h-14 rounded-xl bg-neutral-100 overflow-hidden">
                            <img src="{{ $item->product?->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-neutral-900 line-clamp-1">{{ $item->product_name }}</p>
                            <p class="text-xs text-neutral-400 mt-0.5">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                        </div>
                        <p class="font-extrabold text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 border-t border-neutral-100 pt-4 flex items-center justify-between">
                <span class="font-bold text-neutral-900">Total Pesanan</span>
                <span class="font-extrabold text-ink text-xl">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
                <h3 class="font-extrabold text-neutral-900">Ubah Status</h3>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                        @foreach (\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Simpan Status</button>
                </form>
            </div>

            <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
                <h3 class="font-extrabold text-neutral-900">Informasi Customer</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-neutral-500">Nama</dt><dd class="font-semibold text-neutral-900">{{ $order->customer_name }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-neutral-500">Telepon</dt><dd class="font-semibold text-neutral-900">{{ $order->phone }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-neutral-500">Email</dt><dd class="font-semibold text-neutral-900 truncate">{{ $order->user->email }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-neutral-500">Metode</dt><dd class="font-semibold text-neutral-900">{{ $order->payment_method->label() }}</dd></div>
                </dl>
                <p class="mt-3 text-sm text-neutral-600 border-t border-neutral-100 pt-3">Alamat: {{ $order->address }}</p>
                @if ($order->notes)
                    <p class="mt-2 text-sm text-neutral-500">Catatan: {{ $order->notes }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection