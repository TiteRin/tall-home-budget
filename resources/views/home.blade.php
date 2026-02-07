<x-app-layout title="Accueil">
    <h1 class="text-3xl font-bold text-base-content mb-4 font-cursive text-center md:text-left">
        Foyer {{ $household->name }}
    </h1>
    @livewire('home', ['household' => $household])
</x-app-layout>
