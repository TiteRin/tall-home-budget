@extends('layouts.app')

@section('title', 'Politique de confidentialité')

@section('content')
    <div class="min-h-screen bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body prose dark:prose-invert max-w-none">
                    <h1 class="card-title text-3xl font-bold mb-6">
                        Politique de confidentialité
                    </h1>

                    <p>
                        La présente politique de confidentialité a pour objectif d’informer les utilisateurs
                        de l’application <strong>{{ config('app.name') }}</strong> sur la manière dont leurs données
                        personnelles
                        sont collectées, utilisées et protégées.
                    </p>

                    <p>
                        {{ config('app.name') }} respecte le Règlement Général sur la Protection des Données
                        (<strong>RGPD – UE 2016/679</strong>).
                    </p>

                    <h2>
                        1. Responsable du traitement
                    </h2>

                    <p>
                        Le responsable du traitement des données est l’éditeur de l’application,
                        à titre particulier, basé à Lille (France).
                    </p>

                    <p>
                        Pour toute demande relative aux données personnelles :
                        <a href="mailto:contact@home-budget.titerin.ovh">contact@home-budget.titerin.ovh</a>
                    </p>

                    <h2>
                        2. Données collectées
                    </h2>

                    <p>
                        Dans le cadre de l’utilisation de {{ config('app.name') }}, les données suivantes peuvent être
                        collectées :
                    </p>

                    <ul>
                        <li>Nom</li>
                        <li>Prénom</li>
                        <li>Adresse e-mail</li>
                        <li>Mot de passe (stocké sous forme chiffrée / hashée)</li>
                    </ul>

                    <p>
                        L’application enregistre également :
                    </p>

                    <ul>
                        <li>Des catégories de dépenses</li>
                        <li>Des montants associés</li>
                    </ul>

                    <h2>
                        3. Données non stockées sur le serveur
                    </h2>

                    <p>
                        {{ config('app.name') }} ne stocke pas les revenus des membres sur ses serveurs.
                    </p>

                    <p>
                        Les revenus sont enregistrés uniquement dans le stockage local
                        (<code>localStorage</code>) du navigateur de l’utilisateur.
                    </p>

                    <p>
                        Aucune donnée bancaire n’est demandée ou stockée par {{ config('app.name') }}.
                    </p>

                    <h2>
                        4. Finalités du traitement
                    </h2>

                    <p>
                        Les données personnelles sont collectées uniquement pour :
                    </p>

                    <ul>
                        <li>Créer et gérer un compte utilisateur</li>
                        <li>Permettre l’accès sécurisé à l’application</li>
                        <li>Enregistrer les dépenses et catégories</li>
                        <li>Assurer le bon fonctionnement du service</li>
                    </ul>

                    <h2>
                        5. Base légale
                    </h2>

                    <p>
                        Le traitement des données repose sur :
                    </p>

                    <ul>
                        <li>L’exécution du service demandé par l’utilisateur</li>
                        <li>Le consentement lors de l’inscription</li>
                    </ul>

                    <h2>
                        6. Durée de conservation
                    </h2>

                    <p>
                        Les données sont conservées tant que le compte utilisateur est actif.
                    </p>

                    <p>
                        L’utilisateur peut demander la suppression de son compte à tout moment.
                    </p>

                    <h2>
                        7. Accès administratif
                    </h2>

                    <p>
                        L’éditeur dispose d’un accès administratif limité permettant uniquement :
                    </p>

                    <ul>
                        <li>De consulter la liste des comptes utilisateurs</li>
                        <li>De supprimer un compte en cas de demande ou nécessité technique</li>
                    </ul>

                    <p>
                        L’éditeur ne consulte pas le contenu financier détaillé des utilisateurs.
                    </p>

                    <h2>
                        8. Partage des données
                    </h2>

                    <p>
                        Les données personnelles ne sont ni vendues, ni louées, ni transmises à des tiers.
                    </p>

                    <h2>
                        9. Droits des utilisateurs
                    </h2>

                    <p>
                        Conformément au RGPD, chaque utilisateur dispose des droits suivants :
                    </p>

                    <ul>
                        <li>Droit d’accès</li>
                        <li>Droit de rectification</li>
                        <li>Droit à l’effacement</li>
                        <li>Droit d’opposition</li>
                        <li>Droit à la limitation du traitement</li>
                    </ul>

                    <p>
                        Pour exercer ces droits, l’utilisateur peut contacter :
                        <a href="mailto:contact@home-budget.titerin.ovh">contact@home-budget.titerin.ovh</a>
                    </p>

                    <h2>
                        10. Cookies
                    </h2>

                    <p>
                        {{ config('app.name') }} n’utilise pas de cookies publicitaires ou de suivi.
                    </p>

                    <p>
                        Des cookies strictement techniques peuvent être utilisés pour assurer
                        l’authentification et le fonctionnement du service.
                    </p>

                    <h2>
                        11. Modification de la politique
                    </h2>

                    <p>
                        Cette politique de confidentialité peut être mise à jour à tout moment.
                        La version applicable est celle disponible dans l’application à la date de consultation.
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
