@extends('layouts.app')

@section('title', 'CGU')

@section('content')
    <div class="min-h-screen bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body prose dark:prose-invert max-w-none">
                    <h1 class="card-title text-3xl font-bold mb-6">
                        Conditions Générales d’Utilisation (CGU)
                    </h1>

                    <p>
                        Les présentes Conditions Générales d’Utilisation ont pour objet de définir
                        les modalités d’accès et d’utilisation de l’application
                        <strong>{{ config('app.name') }}</strong>.
                    </p>

                    <h2>
                        1. Objet de l’application
                    </h2>

                    <p>
                        {{ config('app.name') }} est une application permettant aux utilisateurs de gérer leurs
                        dépenses personnelles et catégories associées.
                    </p>

                    <h2>
                        2. Accès au service
                    </h2>

                    <p>
                        L’accès à {{ config('app.name') }} nécessite la création d’un compte utilisateur.
                        L’utilisateur s’engage à fournir des informations exactes lors de son inscription.
                    </p>

                    <h2>
                        3. Responsabilité
                    </h2>

                    <p>
                        {{ config('app.name') }} est proposé à titre d’outil d’organisation.
                        L’éditeur ne garantit pas l’exactitude des résultats ou calculs produits
                        par l’utilisateur.
                    </p>

                    <p>
                        L’utilisateur reste seul responsable de l’usage qu’il fait des informations
                        enregistrées dans l’application.
                    </p>

                    <h2>
                        4. Données personnelles
                    </h2>

                    <p>
                        {{ config('app.name') }} collecte uniquement les données nécessaires à la création du compte
                        (nom, prénom, email, mot de passe chiffré).
                    </p>

                    <p>
                        Les revenus ne sont pas stockés sur les serveurs : ils restent enregistrés
                        localement dans le navigateur de l’utilisateur.
                    </p>

                    <p>
                        Pour plus d’informations, l’utilisateur peut consulter la
                        <a href="{{ route('confidentialite') }}" class="underline">
                            Politique de confidentialité
                        </a>.
                    </p>

                    <h2>
                        5. Suppression du compte
                    </h2>

                    <p>
                        L’utilisateur peut demander la suppression de son compte à tout moment
                        en contactant l’éditeur ou via l’interface prévue dans l’application.
                    </p>

                    <h2>
                        6. Modification des CGU
                    </h2>

                    <p>
                        Les présentes CGU peuvent être modifiées à tout moment.
                        La version applicable est celle disponible dans l’application à la date de consultation.
                    </p>

                    <h2>
                        7. Contact
                    </h2>

                    <p>
                        Pour toute question, l’utilisateur peut contacter :
                        <a href="mailto:contact@home-budget.titerin.ovh">contact@home-budget.titerin.ovh</a>
                    </p>

                    <div class="divider"></div>

                    <p class="text-sm opacity-70">
                        Dernière mise à jour le 27 janvier 2026.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
