<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Forbidden</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-50 font-sans antialiased">
    <div class="text-center px-6">
        <p class="text-6xl font-bold text-brand-600">403</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-900">Access Denied</h1>
        <p class="mt-2 text-sm text-gray-500">You don't have permission to access this page.</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-block rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
            Back to Dashboard
        </a>
    </div>
</body>
</html>
