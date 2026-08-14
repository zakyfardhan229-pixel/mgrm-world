@extends('layouts.admin')

@section('title', 'Pesanan')
@section('page', 'Manajemen Pesanan')

@section('content')
    <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1 sm:w-64">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. pesanan / customer..."
                    class="w-full rounded-full border border-neutral-300 pl-10 pr-4 py-2 text-sm focus:border-ink focus:ring-ink">
            </div>
            <select name="status"
                class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                <option value="">Semua Status</option>
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="rounded-full bg-ink px-4 py-2 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Filter</button>
            @if (request('search') || request('status'))
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center text-sm font-semibold text-neutral-500 hover:text-neutral-900">Reset</a>
            @endif
        </form>

        @if ($orders->isEmpty())
            <div class="mt-8 text-center py-10">
                <p class="font-semibold text-neutral-900">Tidak ada pesanan ditemukan</p>
            </div>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr
                            class="border-b border-neutral-100 text-left text-xs font-bold uppercase tracking-wider text-neutral-400">
                            <th class="pb-3 pr-4">No. Pesanan</th>
                            <th class="pb-3 pr-4">Customer</th>
                            <th class="pb-3 pr-4">Item</th>
                            <th class="pb-3 pr-4">Total</th>
                            <th class="pb-3 pr-4">Metode</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 pr-4">Tanggal</th>
                            <th class="pb-3 pr-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-neutral-100">
                        @forelse ($orders as $order)
                            <tr class="transition-colors hover:bg-neutral-50">

                                {{-- No. Pesanan --}}
                                <td class="py-3.5 pr-4 font-bold text-neutral-900 whitespace-nowrap">
                                    {{ $order->order_number }}
                                </td>

                                {{-- Customer --}}
                                <td class="py-3.5 pr-4">
                                    <div class="font-medium text-neutral-900">
                                        {{ $order->customer_name }}
                                    </div>

                                    <div class="text-xs text-neutral-400">
                                        {{ $order->phone }}
                                    </div>
                                </td>

                                {{-- Item --}}
                                <td class="py-3.5 pr-4">
                                    <div class="max-w-xs space-y-1">
                                        @forelse ($order->items as $item)
                                            <div class="truncate text-neutral-600">
                                                {{ $item->product?->name ?? 'Produk tidak tersedia' }}

                                                <span class="text-neutral-400">
                                                    × {{ $item->quantity }}
                                                </span>
                                            </div>
                                        @empty
                                            <span class="text-neutral-400">—</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Total --}}
                                <td class="py-3.5 pr-4 font-bold text-neutral-900 whitespace-nowrap">
                                    Rp {{ number_format((float) $order->total, 0, ',', '.') }}
                                </td>

                                {{-- Metode --}}
                                <td class="py-3.5 pr-4 text-neutral-500 whitespace-nowrap">
                                    {{ $order->payment_method->label() }}
                                </td>

                                {{-- Status --}}
                                <td class="py-3.5 pr-4">
                                    <span
                                        class="inline-flex rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-bold text-neutral-600">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>

                                {{-- Tanggal --}}
                                <td class="py-3.5 pr-4 text-neutral-500 whitespace-nowrap">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 pr-4 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="inline-flex items-center rounded-lg bg-black px-3 py-1.5 text-sm font-semibold text-white hover:bg-white hover:text-black transition duration-300 ease-in-out">
                                        Edit
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-neutral-400">
                                    Belum ada pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection