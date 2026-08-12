<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MGRM World: @yield('title', 'MGRM World')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/validation.js') }}" defer></script>
</head>

<body class="font-sans antialiased bg-paper text-neutral-900">
    <div x-data="{ mobileOpen: false }" class="min-h-screen flex flex-col">
        <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-40 w-full bg-white border-b border-neutral-200">
            <div class="w-full px-6 sm:px-8 lg:px-10">
                <div class="flex items-center justify-between h-[64px]">

                    {{-- LOGO --}}
                    <a href="{{ route('home') }}" class="shrink-0 leading-none">
                        <span class="text-[20px] sm:text-[21px] font-black tracking-[-1.5px] text-black">
                            MGRMWorld
                        </span>
                    </a>


                    {{-- DESKTOP NAVIGATION --}}
                    <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center gap-7">
                        <a href="{{ route('shop.index') }}"
                            class="text-[9px] lg:text-[12px] font-bold uppercase tracking-[0.02em] text-neutral-800 hover:text-neutral-400 transition-colors">
                            Catalog
                        </a>

                        <a href="{{ route('shop.community') }}"
                            class="text-[9px] lg:text-[12px] font-bold uppercase tracking-[0.02em] text-neutral-800 hover:text-neutral-400 transition-colors">
                            Community
                        </a>

                        <a href="{{ route('shop.about') }}"
                            class="text-[9px] lg:text-[12px] font-bold uppercase tracking-[0.02em] text-neutral-800 hover:text-neutral-400 transition-colors">
                            About
                        </a>
                    </div>


                    {{-- RIGHT SIDE --}}
                    <div class="flex items-center gap-4 ml-auto">

                        {{-- CURRENCY --}}
                        <button type="button"
                            class="hidden sm:flex items-center gap-1.5 text-[12px] font-bold uppercase text-neutral-800 hover:text-neutral-400 transition-colors">
                            <span class="inline-block w-[10px] h-[7px] bg-[#e50000]"></span>
                        </button>


                        @auth

                            {{-- CART --}}
                            <a href="{{ route('cart.index') }}"
                                class="relative text-[12px] font-bold uppercase text-neutral-800 hover:text-neutral-400 transition-colors">
                                CART

                                @php
                                    $cartCount = auth()->user()
                                        ->cartItems()
                                        ->sum('quantity');
                                @endphp

                                @if ($cartCount > 0)
                                    <span
                                        class="absolute -top-2 -right-3 min-w-[13px] h-[13px] px-1 flex items-center justify-center rounded-full bg-black text-white text-[7px] font-bold">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>


                            {{-- USER --}}
                            <div class="hidden sm:block">
                                <x-dropdown align="right" width="48">

                                    <x-slot name="trigger">
                                        <button type="button"
                                            class="flex items-center justify-center text-neutral-800 hover:text-neutral-400 transition-colors">
                                            <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor"
                                                stroke-width="1.4" viewBox="0 0 24 24">
                                                {{-- Head --}}
                                                <circle cx="12" cy="8" r="3.2" />

                                                {{-- Body --}}
                                                <path stroke-linecap="round"
                                                    d="M5.5 20c.7-3.8 3-5.7 6.5-5.7s5.8 1.9 6.5 5.7" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            Profil
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('orders.index')">
                                            Pesanan Saya
                                        </x-dropdown-link>

                                        @if (auth()->user()->isAdmin())
                                            <x-dropdown-link :href="route('admin.dashboard')">
                                                Admin Panel
                                            </x-dropdown-link>
                                        @endif

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf

                                            <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                                Keluar
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>

                                </x-dropdown>
                            </div>
                        @else

                            {{-- GUEST --}}
                            <a href="{{ route('login') }}"
                                class="text-[12px] font-bold uppercase text-neutral-800 hover:text-neutral-400 transition-colors">
                                Login
                            </a>

                            <a href="{{ route('register') }}"
                                class="text-[12px] font-bold uppercase text-neutral-800 hover:text-neutral-400 transition-colors">
                                Register
                            </a>

                        @endauth


                        {{-- MOBILE MENU BUTTON --}}
                        <button @click="mobileOpen = !mobileOpen" type="button"
                            class="md:hidden flex items-center justify-center text-black" aria-label="Toggle menu">
                            <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>

                            <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>

                    </div>
                </div>
            </div>


            {{-- MOBILE NAVIGATION --}}
            <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-neutral-200 bg-white">
                <div class="px-6 py-5 space-y-4">

                    <a href="{{ route('shop.index') }}"
                        class="block text-[10px] font-bold uppercase tracking-wide text-black">
                        Catalog
                    </a>

                    <a href="{{ route('shop.community') }}"
                        class="block text-[10px] font-bold uppercase tracking-wide text-neutral-600">
                        Community
                    </a>

                    <a href="{{ route('shop.about') }}"
                        class="block text-[10px] font-bold uppercase tracking-wide text-neutral-600">
                        About
                    </a>

                    @auth

                        <div class="pt-3 border-t border-neutral-100 space-y-4">

                            <a href="{{ route('orders.index') }}"
                                class="block text-[10px] font-bold uppercase tracking-wide text-neutral-600">
                                Pesanan Saya
                            </a>

                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="block text-[10px] font-bold uppercase tracking-wide text-neutral-600">
                                    Admin Panel
                                </a>
                            @endif

                            <a href="{{ route('profile.edit') }}"
                                class="block text-[10px] font-bold uppercase tracking-wide text-neutral-600">
                                Profil
                            </a>
                        </div>

                        <div class="pt-3 border-t border-neutral-100 space-y-4">
                            {{-- MOBILE LOGOUT --}}
                            <form method="POST" action="{{ route('logout') }}" class="sm:hidden">
                                @csrf

                                <button type="submit">
                                    <a class="block text-[10px] font-bold uppercase tracking-wide text-neutral-600">
                                        Logout
                                    </a>
                                </button>
                            </form>
                        </div>

                    @endauth

                </div>
            </div>
        </nav>


        @if (session('success'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
                <div
                    class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium shadow-card">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
                <div
                    class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm font-medium shadow-card">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="bg-ink text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex items-center justify-center gap-4">
                    <p class="text-sm text-neutral-400">&copy; {{ date('Y') }} MGRM World. All rights reserved.</p>
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>