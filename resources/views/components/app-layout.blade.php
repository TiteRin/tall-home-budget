<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}{{ $title ?? ""  }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="min-h-screen antialiased bg-base-200">

<header class="shadow px-4 py-3 sticky top-0 z-10 bg-base-100 hidden md:block">
    <div class="max-w-7xl mx-auto">
        <x-navigation/>
    </div>
</header>

<livewire:onboarding/>

<main class="px-4 py-4 pb-[calc(theme(spacing.8)+100px+env(safe-area-inset-bottom))] md:pb-4">
    <div class="max-w-7xl mx-auto">
        {{ $slot }}
    </div>
</main>

<footer
    class="fixed inset-x-0 bottom-0 z-50 border-t bg-base-100/95 backdrop-blur
    pb-[env(safe-area-inset-bottom)] md:hidden">

    <x-navigation-mobile/>
</footer>

@livewireScripts
@vite('resources/js/app.js')
</body>
</html>
