<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bayside Pavers') }} - Login</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-brand-950 font-sans antialiased">
    <div class="w-full max-w-md px-6">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Bayside Pavers" class="mx-auto mb-6 h-14 w-auto drop-shadow-lg">
            <p class="text-sm text-gray-400">Commission Management System</p>
        </div>

        <!-- Card -->
        <div class="rounded-2xl bg-white p-8 shadow-2xl ring-1 ring-white/10">
            {{ $slot }}
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">&copy; {{ date('Y') }} Bayside Pavers. All rights reserved.</p>
    </div>

    @livewireScripts
</body>
</html>
