<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MGRM World: {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/validation.js') }}" defer></script>
</head>

<body class="font-sans antialiased text-neutral-900">
    <div class="min-h-screen flex flex-col sm:justify-center items-center py-10 bg-paper">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <!-- <span
                class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-ink text-white font-extrabold text-lg shadow-card">Z</span> -->
            <span class="font-extrabold text-xl tracking-tight">MGRM World</span>
        </a>

        <div
            class="w-full max-w-md mt-5 sm:mt-6 px-4 py-6 sm:px-6 sm:py-8 bg-white shadow-elevated rounded-lg sm:rounded-lg border border-neutral-200/70">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-neutral-400">&copy; {{ date('Y') }} MGRM World</p>
    </div>
</body>