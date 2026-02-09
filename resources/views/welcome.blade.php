@extends('layouts.app')

@section('content')
    <div
        class="relative min-h-screen bg-base-200 bg-center sm:flex sm:justify-center sm:items-center selection:bg-primary selection:text-primary-content">
        <div class="p-6 mx-auto max-w-7xl lg:p-8">
            <div class="flex flex-col items-center">
                <h1 class="text-6xl font-bold text-base-content mb-4 cedarville-cursive-regular sr-only">
                    {{ config('app.name') }}
                </h1>
                <img src="/assets/img/logo.svg" alt="Logo de l'application" class="w-full h-48 mb-2">
                <p class="text-xl text-base-content/70 text-center mb-8">
                    En couple ? En colocation ? Gérez votre budget domestique en tout simplicité !<br/>
                    Mettez vos comptes bien à plat !
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

            <div
                class="mt-[30px] lg:mt-[-90px] lg:pt-[170px] lg:bg-[url(/public/assets/img/scene.png)] lg:bg-no-repeat lg:min-w-[1011px] lg:min-h-[792px]">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="card bg-base-100 card-lg shadow-xl relative">
                        <div class="card-body">
                            <h3 class="card-title text-3xl font-semibold text-base-content mb-2 font-cursive">
                                Créez votre foyer
                            </h3>
                            <p class="text-base-content/70">
                                Ajoutez et invitez les membres de votre foyer à l’application. <br/>
                                Est-ce que vous êtes plutôt 50/50 ou partage au prorata ? <br/>
                                <br/>
                                <a href="{{ route('distribution-method') }}" class="link link-primary">Cliquez ici pour
                                    en
                                    savoir plus sur les modes de partages</a>
                            </p>
                            <img src="/assets/img/humaan-a.png" alt=""
                                 class="hidden lg:absolute lg:block bottom-[-370px] right-[-130px]"/>
                        </div>
                    </div>
                    <div class="card bg-base-100 card-lg shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-3xl font-semibold text-base-content mb-2 font-cursive">Ajoutez
                                vos
                                charges</h3>
                            <p class="text-base-content/70">
                                Le loyer, l’électricité, Internet… Ces dépenses fixes qui tombent tous les mois à la
                                même
                                date, ajoutez les <strong>une seule fois</strong>.<br/>
                                Associez à chaque charge un membre et un mode de partage, et c’est réglé !

                            </p>
                            <img src="/assets/img/humaan-b.png" alt=""
                                 class="hidden lg:absolute lg:block bottom-[-370px] left-[30px]"/>
                        </div>
                    </div>
                    <div class="card bg-base-100 card-lg shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-3xl font-semibold text-base-content mb-2 font-cursive">Saisissez
                                vos
                                revenus</h3>
                            <p class="text-base-content/70">
                                Revenez chaque mois dans l’application pour renseigner les revenus de chacun. <br/>
                                Immédiatement, obtenez la répartition des montants. Plus besoin de faire de calculs
                                compliqués !
                            </p>
                        </div>
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
