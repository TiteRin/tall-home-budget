<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Application Charges Foyer' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    <header class="bg-white shadow p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">🏠 Mon Foyer</h1>
            {{-- Tu pourras ajouter ici une nav plus tard --}}
        </div>
    </header>

    <main class="container mx-auto px-4">
        {{ $slot }}
    </main>

    @livewireScripts
    @vite('resources/js/app.js')
</body>
</html>
