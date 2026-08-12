@extends('layouts.admin')

@section('title', 'Produk')
@section('page', 'Manajemen Produk')

@section('content')
    <div x-data="{ qrProduct: null }" class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <form action="{{ route('admin.produk.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <div class="relative flex-1 sm:w-56">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                        class="w-full rounded-full border border-neutral-300 pl-10 pr-4 py-2 text-sm focus:border-ink focus:ring-ink">
                </div>
                <select name="category"
                    class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status"
                    class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <select name="featured"
                    class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    <option value="">Semua Featured</option>
                    <option value="featured" @selected(request('featured') === 'featured')>Featured</option>
                    <option value="not_featured" @selected(request('featured') === 'not_featured')>Non-Featured</option>
                </select>
                <select name="availability"
                    class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    <option value="">Semua Stok</option>
                    <option value="in_stock" @selected(request('availability') === 'in_stock')>Tersedia</option>
                    <option value="out_of_stock" @selected(request('availability') === 'out_of_stock')>Stok Habis</option>
                </select>
                <select name="color"
                    class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    <option value="">Semua Warna</option>
                    @foreach ($colors as $color)
                        <option value="{{ $color }}" @selected(request('color') === $color)>{{ ucfirst($color) }}</option>
                    @endforeach
                </select>
                <select name="size"
                    class="rounded-full border border-neutral-300 px-4 py-2 text-sm focus:border-ink focus:ring-ink">
                    <option value="">Semua Ukuran</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size }}" @selected(request('size') === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="rounded-full bg-ink px-4 py-2 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Filter</button>
                @if (request('search') || request('category') || request('status') || request('featured') || request('availability') || request('color') || request('size'))
                    <a href="{{ route('admin.produk.index') }}"
                        class="inline-flex items-center text-sm font-semibold text-neutral-500 hover:text-neutral-900">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.produk.create') }}"
                class="inline-flex items-center justify-center rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">+
                Tambah Produk</a>
        </div>

        @if ($products->isEmpty())
            <div class="mt-8 text-center py-10">
                <p class="font-semibold text-neutral-900">Tidak ada produk ditemukan</p>
            </div>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr
                            class="text-left text-xs font-bold uppercase tracking-wider text-neutral-400 border-b border-neutral-100">
                            <th class="pb-3 pr-4">Produk</th>
                            <th class="pb-3 pr-4">Kategori</th>
                            <th class="pb-3 pr-4">Harga</th>
                            <th class="pb-3 pr-4">Stok</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($products as $product)
                            <tr>
                                <td class="py-3.5 pr-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                            class="w-11 h-11 rounded-xl object-cover bg-neutral-100">
                                        <span class="font-bold text-neutral-900 max-w-48 truncate">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 pr-4"><span
                                        class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-bold text-neutral-600">{{ $product->category->name }}</span>
                                </td>
                                <td class="py-3.5 pr-4 font-semibold text-neutral-900">Rp
                                    {{ number_format((float) $product->price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 pr-4">
                                    <span
                                        class="font-semibold {{ $product->stock > 0 ? 'text-neutral-900' : 'text-red-600' }}">{{ $product->stock }}</span>
                                </td>
                                <td class="py-3.5 pr-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-neutral-100 text-neutral-500' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            class="rounded-full bg-ink px-3.5 py-1.5 text-xs font-bold text-white shadow-card hover:shadow-hover transition"
                                            data-name="{{ $product->name }}"
                                            data-url="{{ route('admin.produk.qr', $product) }}"
                                            @click="qrProduct = { name: $event.currentTarget.dataset.name, url: $event.currentTarget.dataset.url }">QR</button>
                                        <a href="{{ route('admin.produk.edit', $product) }}"
                                            class="rounded-full bg-neutral-100 px-3.5 py-1.5 text-xs font-bold text-neutral-700 hover:bg-neutral-200 transition">Edit</a>
                                        <form action="{{ route('admin.produk.destroy', $product) }}" method="POST"
                                            onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="rounded-full bg-red-50 px-3.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif

        {{-- QR MODAL --}}
        <div x-show="qrProduct !== null"
            class="fixed inset-0 z-40 flex items-center justify-center p-4" style="display: none;">
            <div class="absolute inset-0 bg-black/40" @click="qrProduct = null"></div>

            <div class="relative bg-white rounded-2xl p-6 max-w-xs w-full shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-neutral-900 truncate pr-3" x-text="qrProduct?.name ?? ''"></h3>
                    <button type="button" @click="qrProduct = null"
                        class="shrink-0 text-neutral-400 hover:text-neutral-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <img :src="qrProduct?.url ?? ''" alt="QR Code Produk"
                    class="w-full object-contain border border-neutral-100 rounded-xl">

                <div class="mt-4 flex items-center justify-center">
                    <a :href="qrProduct?.url ?? ''" download
                        class="rounded-full bg-ink px-5 py-2 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                        Download SVG
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection