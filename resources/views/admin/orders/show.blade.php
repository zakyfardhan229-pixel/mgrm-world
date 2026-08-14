@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('page', 'Detail Pesanan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-neutral-900">{{ $order->order_number }}</h2>
            <p class="text-sm text-neutral-500 mt-1">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full px-4 py-2 text-xs font-bold
                @if ($order->status->value === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                @elseif ($order->status->value === 'cancelled') bg-red-50 text-red-700 border border-red-200
                @elseif ($order->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                @else bg-neutral-100 text-neutral-700 border border-neutral-200 @endif">
                {{ $order->status->label() }}
            </span>
            <a href="{{ route('admin.orders.index') }}"
                class="rounded-full bg-white border border-neutral-300 px-4 py-2 text-xs font-bold text-neutral-700 hover:bg-neutral-100 transition">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        {{-- Item Pesanan --}}
        <div class="xl:col-span-2 bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <h3 class="text-lg font-extrabold text-neutral-900">Item Pesanan</h3>
            <div class="mt-4 divide-y divide-neutral-100">
                @forelse ($order->items as $item)
                    <div class="py-4 flex items-center gap-4">
                        <div class="shrink-0 w-16 h-16 rounded-lg bg-neutral-100 overflow-hidden flex-shrink-0">
                            <img src="{{ $item->product?->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-neutral-900 line-clamp-2">{{ $item->product_name }}</p>
                            <p class="text-sm text-neutral-500 mt-1">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-extrabold text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-neutral-400">
                        <p>Tidak ada item dalam pesanan</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 border-t border-neutral-100 pt-4 flex items-center justify-between bg-neutral-50 -mx-6 -mb-6 px-6 py-4 rounded-b-3xl">
                <span class="font-semibold text-neutral-900">Total Pesanan</span>
                <span class="font-extrabold text-ink text-2xl">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Ubah Status --}}
            <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
                <h3 class="text-lg font-extrabold text-neutral-900">Ubah Status</h3>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="status" class="block text-sm font-semibold text-neutral-700 mb-2">Status Pesanan</label>
                        <select name="status" id="status" class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink transition">
                            @foreach (\App\Enums\OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($order->status === $status)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            {{-- Informasi Customer --}}
            <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
                <h3 class="text-lg font-extrabold text-neutral-900">Informasi Customer</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-neutral-500 font-medium">Nama</dt>
                        <dd class="font-semibold text-neutral-900 text-right">{{ $order->customer_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-neutral-500 font-medium">Telepon</dt>
                        <dd class="font-semibold text-neutral-900 text-right">{{ $order->phone }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-neutral-500 font-medium">Email</dt>
                        <dd class="font-semibold text-neutral-900 text-right truncate">{{ $order->user->email }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-neutral-500 font-medium">Metode</dt>
                        <dd class="font-semibold text-neutral-900 text-right">{{ $order->payment_method->label() }}</dd>
                    </div>
                </dl>
                
                <div class="mt-4 pt-4 border-t border-neutral-100">
                    <h4 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Alamat Pengiriman</h4>
                    <p class="text-sm text-neutral-700 leading-relaxed">{{ $order->address }}</p>
                </div>

                @if ($order->notes)
                    <div class="mt-4 pt-4 border-t border-neutral-100">
                        <h4 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Catatan</h4>
                        <p class="text-sm text-neutral-700 leading-relaxed">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection