@extends('layouts.app')

@section('content')
    <div
        class="relative min-h-screen bg-base-200 bg-center sm:flex sm:justify-center sm:items-center selection:bg-primary selection:text-primary-content">
        <div class="p-6 mx-auto max-w-7xl lg:p-8">
            <div class="flex flex-col items-center">
                <h1 class="text-6xl font-bold text-base-content mb-4">
                    Home Budget
                </h1>
                <p class="text-xl text-base-content/70 text-center mb-8">
                    Gérez votre budget domestique en toute simplicité. Suivez vos dépenses, organisez vos factures et
                    gardez un œil sur vos finances.
                </p>

                <div class="flex gap-4">
                    <a href="{{ route('register') }}"
                       class="btn btn-primary">
                        Créer un compte
                    </a>
                    <a href="{{ route('login') }}"
                       class="btn btn-outline btn-primary">
                        Se connecter
                    </a>
                </div>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg font-semibold text-base-content mb-2">Suivi des dépenses</h3>
                        <p class="text-base-content/70">Enregistrez et catégorisez vos dépenses régulières pour
                            voir où va votre argent.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg font-semibold text-base-content mb-2">Gestion du foyer</h3>
                        <p class="text-base-content/70">Gerez les finances partagées avec les membres de votre
                            foyer en toute transparence.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg font-semibold text-base-content mb-2">« Les bons comptes… »</h3>
                        <p class="text-base-content/70">Obtenez en temps réel la répartition des montants dûs
                            par chacun.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-center px-0 mt-16 sm:items-center sm:justify-between">
                <div class="text-sm text-center text-base-content/50 sm:text-left">
                    &nbsp;
                </div>
                <div class="ml-4 text-sm text-center text-base-content/50 sm:text-right sm:ml-0">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </div>
@endsection
