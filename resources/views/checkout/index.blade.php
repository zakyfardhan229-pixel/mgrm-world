@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
    @if ($cartItems->isEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <div class="rounded-lg bg-white border border-neutral-200 shadow-card p-12 text-center">
                <p class="text-lg font-semibold text-neutral-900">Keranjang kosong, tidak ada yang bisa dibayar.</p>
                <a href="{{ route('shop.index') }}"
                    class="mt-6 inline-flex items-center rounded-full bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Lihat
                    Katalog</a>
            </div>
        </section>
    @else
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-900">MGRM World</h1>

            @if ($buyNow)
                <p class="mt-2 text-sm text-neutral-500">
                    Silahkan isi detail dibawah ini untuk melanjutkan Pemesanan.
                </p>
            @endif

            @if ($invalidItems->isNotEmpty())
                <div class="mt-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm font-medium">
                    Beberapa produk tidak dapat dipesan:
                    <ul class="mt-1.5 list-disc list-inside space-y-0.5">
                        @foreach ($invalidItems as $item)
                            <li>
                                {{ $item->product->name }}
                                @if (!$item->product->is_active)
                                    (produk tidak aktif)
                                @else
                                    (stok tersisa {{ $item->product->stock }}, permintaan {{ $item->quantity }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    Perbarui keranjang Anda sebelum membuat pesanan.
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST"
                class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start" data-validate x-data="{
                                    paymentMethod: '{{ old('payment_method', 'transfer') }}',
                                    qrData: null,
                                    generating: false,
                                    qrisToken: null,
                                    pollTimer: null,
                                    checkQrisStatus() {
                                        if (!this.qrisToken) return;
                                        fetch('{{ route('checkout.qris.status', ['token' => '__TOKEN__']) }}'.replace('__TOKEN__', this.qrisToken))
                                            .then(r => r.json())
                                            .then(d => {
                                                if (d.status === 'confirmed') {
                                                    clearInterval(this.pollTimer);
                                                    this.pollTimer = null;
                                                    window.location.href = '/pesanan/' + d.order_id;
                                                } else if (d.status === 'expired') {
                                                    clearInterval(this.pollTimer);
                                                    this.pollTimer = null;
                                                }
                                            })
                                            .catch(() => {});
                                    }
                                }" x-ref="checkoutForm">
                @csrf

                <div class="lg:col-span-2 bg-white rounded-lg border border-neutral-200 shadow-card p-6">
                    <h2 class="font-extrabold text-neutral-900">Data Pengiriman</h2>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-semibold text-neutral-700">Nama
                                Penerima</label>
                            <input type="text" id="customer_name" name="customer_name"
                                value="{{ old('customer_name', auth()->user()->name) }}" required maxlength="150" data-required
                                data-label="Nama penerima"
                                class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                            @error('customer_name')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-neutral-700">No. HP / Telepon</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required maxlength="20"
                                    data-required data-label="No. HP / telepon" data-pattern="^[0-9+\-\s]{8,20}$"
                                    data-pattern-message="Nomor telepon tidak valid"
                                    class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                                @error('phone')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="payment_method" class="block text-sm font-semibold text-neutral-700">Metode
                                    Pembayaran</label>
                                <select id="payment_method" name="payment_method" required data-required
                                    data-label="Metode pembayaran" x-model="paymentMethod"
                                    class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Bank Transfer
                                    </option>
                                    <option value="cod" @selected(old('payment_method') === 'cod')>COD (Bayar di Tempat)</option>
                                    <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                                </select>
                                @error('payment_method')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-semibold text-neutral-700">Alamat Lengkap</label>
                            <textarea id="address" name="address" rows="3" required maxlength="1000" data-required
                                data-label="Alamat lengkap"
                                class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">{{ old('address') }}</textarea>
                            @error('address')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-semibold text-neutral-700">Catatan (opsional)</label>
                            <input type="text" id="notes" name="notes" value="{{ old('notes') }}" maxlength="1000"
                                class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                            @error('notes')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- QRIS --}}
                        <div x-show="paymentMethod === 'qris'" x-cloak class="mt-2">
                            <button type="button" @click="generating = true;
                                                                            if (qrData) URL.revokeObjectURL(qrData);
                                                                            qrData = null;
                                                                            if (pollTimer) clearInterval(pollTimer);
                                                                            pollTimer = null;
                                                                            fetch('{{ route('checkout.qris.generate') }}', {
                                                                                method: 'POST',
                                                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                                                body: new FormData($refs.checkoutForm)
                                                                            })
                                                                                .then(r => { generating = false; const ct = r.headers.get('Content-Type') || ''; if (!r.ok || !ct.includes('application/json')) { alert('Gagal membuat QRIS. Pastikan data terisi, nomor HP valid, dan stok tersedia.'); throw new Error('failed'); } return r.json(); })
                                                                                .then(data => {
                                                                                    qrData = URL.createObjectURL(new Blob([data.svg], { type: 'image/svg+xml' }));
                                                                                    qrisToken = data.token;
                                                                                    pollTimer = setInterval(() => checkQrisStatus(), 3000);
                                                                                })
                                                                                .catch(() => {})" :disabled="generating"
                                class="w-full rounded-full bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                                <span x-show="!generating">Generate QRIS</span>
                                <span x-show="generating">Memuat...</span>
                            </button>

                            <p x-show="!qrisToken" class="mt-2 text-[10px] text-neutral-400 text-center">
                                Pindai dengan HP Anda untuk menyelesaikan pembayaran QRIS.
                            </p>

                            <img x-show="qrData" :src="qrData" alt="QRIS" class="mx-auto mt-3 h-52 w-52 object-contain">

                            <p x-show="qrisToken && !generating"
                                class="mt-3 text-center text-sm font-semibold text-emerald-600">
                                Menunggu konfirmasi pembayaran dari HP...
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-neutral-200 shadow-card p-6 sticky top-20">
                    <h2 class="font-extrabold text-neutral-900">Ringkasan Pesanan</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach ($cartItems as $item)
                            <li class="flex items-center justify-between gap-3">
                                <span class="text-neutral-600 line-clamp-1">{{ $item->product->name }} <span
                                        class="text-neutral-400">x{{ $item->quantity }}</span></span>
                                <span class="font-semibold text-neutral-900">Rp
                                    {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 border-t border-neutral-100 pt-4 flex items-center justify-between">
                        <span class="font-bold text-neutral-900">Total</span>
                        <span class="font-extrabold text-ink text-lg">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                    </div>
                    <button type="submit"
                        class="mt-6 w-full rounded-lg bg-ink px-6 py-3 text-sm font-bold text-white shadow-card hover:shadow-hover transition"
                        x-show="paymentMethod !== 'qris'">
                        Buat Pesanan
                    </button>
                    <p class="mt-3 text-xs text-neutral-400 text-center">Pesanan akan diproses oleh admin setelah konfirmasi.
                    </p>
                </div>
            </form>
        </section>
    @endif
@endsection