<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Application Charges Foyer' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="min-h-screen antialiased bg-base-100">

    <header class="shadow px-4 py-3 sticky top-0 z-10 bg-base-100">
        <div class="max-w-7xl mx-auto">
            <x-navigation />
        </div>
    </header>

    <main class="px-4 py-4">
        <div class="max-w-7xl mx-auto">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>
