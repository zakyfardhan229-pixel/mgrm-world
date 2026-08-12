@extends('layouts.shop')

@section('title', 'About')

@section('content')

    <div class="max-w-3xl mx-auto px-6 py-16 md:py-24">

        <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 mb-8">
            Mengenal MGRM World
        </h1>

        <div class="space-y-6 text-sm leading-relaxed text-neutral-700">

            <p>
                MGRM World adalah toko online yang berfokus menyajikan produk
                pilihan dengan kualitas terbaik dan layanan yang mudah. Dari
                proses pemilihan produk hingga pengiriman, kami berusaha
                memberikan pengalaman belanja yang nyaman dan transparan.
            </p>

            <p>
                Setiap produk yang kami tampilkan dikurasi langsung oleh tim
                kami, memastikan hanya barang dengan kualitas terbaik yang
                sampai ke tangan Anda.
            </p>

            <div class="grid md:grid-cols-2 gap-6 pt-4">

                <div class="border border-neutral-200 p-6">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-neutral-900 mb-2">Visi</h2>
                    <p class="text-neutral-600">
                        Menjadi toko online tepercaya yang menghadirkan produk
                        berkualitas bagi setiap pelanggan di Indonesia.
                    </p>
                </div>

                <div class="border border-neutral-200 p-6">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-neutral-900 mb-2">Misi</h2>
                    <p class="text-neutral-600">
                        Memberikan layanan terbaik, memastikan kualitas produk,
                        dan membangun hubungan jangka panjang dengan pelanggan.
                    </p>
                </div>

            </div>

            <p class="pt-2">
                Untuk pertanyaan lebih lanjut, silakan hubungi kami melalui
                halaman keranjang saat checkout atau melalui email mgrmcatalog@gmail.com.
            </p>

        </div>

    </div>

@endsection