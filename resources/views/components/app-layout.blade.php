<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Application Charges Foyer' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="min-h-screen antialiased">

    <header class="shadow px-4 py-3 sticky top-0 z-10">
        <x-navigation />
    </header>

    <main class="px-4 py-4">
        {{ $slot }}
    </main>

    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>
