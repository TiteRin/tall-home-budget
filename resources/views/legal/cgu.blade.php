@extends('layouts.app')

@section('title', 'CGU')

@section('content')
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="max-w-3xl mx-auto px-6 py-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Conditions Générales d’Utilisation</h1>

            <div class="mt-6 space-y-4 text-gray-700 dark:text-gray-300">
                <p>
                    Cette page est à compléter avec tes CGU (utilisation du service, responsabilités, etc.).
                </p>
                <p class="text-sm opacity-80">
                    Dernière mise à jour : {{ now()->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
@endsection
