@extends('layouts.shop')

@section('title', 'Beranda')

@section('content')

    <!-- HERO SECTION -->
    <section class="relative w-full h-[100vh] min-h-[600px] overflow-hidden">

        <!-- Hero Image -->
        <picture>
            <!-- Mobile: menggunakan gambar khusus mobile -->
            <source media="(max-width: 767px)" srcset="{{ asset('hero-mobile.jpg') }}">

            <!-- Desktop / Tablet -->
            <img src="{{ asset('hero.jpg') }}" alt="Hero Image" class="absolute inset-0 w-full h-full object-cover">
        </picture>

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/10"></div>

        <!-- Hero Content -->
        <div class="absolute inset-0 flex items-center justify-center">

            <a href="{{ route('shop.index') }}" class="px-5 py-2.5 rounded-full border border-white
                                       bg-black/30 backdrop-blur-sm
                                       text-white text-xs font-bold
                                       hover:bg-white hover:text-black
                                       transition duration-300">
                SHOP NOW
            </a>

        </div>

    </section>

@endsection