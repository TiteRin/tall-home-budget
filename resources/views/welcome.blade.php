@extends('layouts.app')

@section('content')
    <div
        class="relative min-h-screen bg-gray-100 bg-center sm:flex sm:justify-center sm:items-center dark:bg-gray-900 selection:bg-indigo-500 selection:text-white">
        <div class="p-6 mx-auto max-w-7xl lg:p-8">
            <div class="flex flex-col items-center">
                <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">
                    Home Budget
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 text-center mb-8">
                    Gérez votre budget domestique en toute simplicité. Suivez vos dépenses, organisez vos factures et
                    gardez un œil sur vos finances.
                </p>

                <div class="flex gap-4">
                    <a href="{{ route('register') }}"
                       class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                        Créer un compte
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg shadow-md border border-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                        Se connecter
                    </a>
                </div>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Suivi des dépenses</h3>
                    <p class="text-gray-600 dark:text-gray-400">Enregistrez et catégorisez vos dépenses régulières pour
                        voir où va votre argent.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Gestion du foyer</h3>
                    <p class="text-gray-600 dark:text-gray-400">Gérez les finances partagées avec les membres de votre
                        foyer en toute transparence.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">« Les bons comptes… »</h3>
                    <p class="text-gray-600 dark:text-gray-400">Obtenez en temps réel la répartition des montants dûs
                        par chacun.</p>
                </div>
            </div>

            <div class="flex justify-center px-0 mt-16 sm:items-center sm:justify-between">
                <div class="text-sm text-center text-gray-500 dark:text-gray-400 sm:text-left">
                    &nbsp;
                </div>
                <div class="ml-4 text-sm text-center text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </div>
@endsection
