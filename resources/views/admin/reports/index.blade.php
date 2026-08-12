@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('page', 'Rekap Laporan Penjualan')

@section('content')
    <div id="report-section">
        <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6 print:hidden">
            <form action="{{ route('admin.reports.index') }}" method="GET"
                class="flex flex-col md:flex-row gap-3 md:items-end">
                <div>
                    <label for="from" class="block text-sm font-semibold text-neutral-700">Dari Tanggal</label>
                    <input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}"
                        class="mt-1.5 rounded-xl border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    @error('from')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="to" class="block text-sm font-semibold text-neutral-700">Sampai Tanggal</label>
                    <input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}"
                        class="mt-1.5 rounded-xl border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    @error('to')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-neutral-700">Status</label>
                    <select id="status" name="status"
                        class="mt-1.5 rounded-xl border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                        <option value="">Semua Status</option>
                        @foreach (\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Tampilkan</button>
                    <a href="{{ route('admin.reports.index') }}"
                        class="rounded-full bg-white border border-neutral-300 px-5 py-2.5 text-sm font-bold text-neutral-700 hover:bg-neutral-100 transition">Reset</a>
                    <button type="button" onclick="window.print()"
                        class="rounded-full bg-white border border-neutral-300 px-5 py-2.5 text-sm font-bold text-neutral-700 hover:bg-neutral-100 transition">Cetak</button>
                </div>
            </form>
            @if ($errors->any())
                <div class="mt-3">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs font-semibold text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-6 bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
            <div class="print-header">
                <h2 class="text-lg font-extrabold text-neutral-900">Rekap Penjualan MGRM World</h2>
                <p class="text-sm text-neutral-500 mt-0.5">
                    Periode:
                    {{ isset($filters['from']) ? \Illuminate\Support\Carbon::parse($filters['from'])->translatedFormat('d F Y') : 'Awal' }}
                    &ndash;
                    {{ isset($filters['to']) ? \Illuminate\Support\Carbon::parse($filters['to'])->translatedFormat('d F Y') : 'Sekarang' }}
                    @if (isset($filters['status'])) · {{ \App\Enums\OrderStatus::from($filters['status'])->label() }} @endif
                </p>
                <p class="text-sm text-neutral-400">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">
                <div class="rounded-2xl bg-neutral-50 border border-neutral-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Transaksi</p>
                    <p class="mt-1 text-2xl font-extrabold text-ink">{{ number_format($totalOrders, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-neutral-50 border border-neutral-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-neutral-400">Item Terjual</p>
                    <p class="mt-1 text-2xl font-extrabold text-ink">{{ number_format($itemsSold, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-ink text-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-neutral-400">Pendapatan</p>
                    <p class="mt-1 text-2xl font-extrabold">Rp {{ number_format((float) $revenue, 0, ',', '.') }}</p>
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="mt-8 text-center py-10">
                    <p class="font-semibold text-neutral-900">Tidak ada transaksi pada periode ini</p>
                </div>
            @else
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead>
                            <tr
                                class="text-left text-xs font-bold uppercase tracking-wider text-neutral-400 border-b border-neutral-100">
                                <th class="pb-3 pr-4">No. Pesanan</th>
                                <th class="pb-3 pr-4">Tanggal</th>
                                <th class="pb-3 pr-4">Customer</th>
                                <th class="pb-3 pr-4">Item</th>
                                <th class="pb-3 pr-4">Metode</th>
                                <th class="pb-3 pr-4">Status</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="py-3.5 pr-4 font-bold text-ink">{{ $order->order_number }}</td>
                                    <td class="py-3.5 pr-4 text-neutral-500 whitespace-nowrap">
                                        {{ $order->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-3.5 pr-4 text-neutral-600">{{ $order->customer_name }}</td>
                                    <td class="py-3.5 pr-4 text-neutral-600">{{ $order->total_items }}</td>
                                    <td class="py-3.5 pr-4 text-neutral-600">{{ $order->payment_method->label() }}</td>
                                    <td class="py-3.5 pr-4">
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold
                                                                    @if ($order->status->value === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                                                    @elseif ($order->status->value === 'cancelled') bg-red-50 text-red-700 border border-red-200
                                                                    @elseif ($order->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                                                    @else bg-neutral-100 text-neutral-700 @endif">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-right font-semibold text-neutral-900">Rp
                                        {{ number_format((float) $order->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 print:hidden">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            body {
                background: #fff !important;
            }

            .print-header {
                display: block !important;
            }

            @page {
                margin: 14mm;
            }
        }

        .print-header {
            display: none;
        }
    </style>
@endsection