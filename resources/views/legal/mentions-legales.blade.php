@extends('layouts.app')

@section('title', 'Mentions légales')

@section('content')
    <div class="min-h-screen bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body prose dark:prose-invert max-w-none">
                    <h1 class="card-title text-3xl font-bold mb-6">
                        Mentions légales
                    </h1>

                    <p>
                        Conformément aux dispositions de la loi n°2004-575 du 21 juin 2004
                        pour la confiance dans l’économie numérique (LCEN), il est précisé aux
                        utilisateurs de l’application <strong>HomeBudget</strong> l’identité des
                        différents intervenants dans le cadre de sa réalisation et de son suivi.
                    </p>

                    <h2>
                        Éditeur de l’application
                    </h2>

                    <p>
                        HomeBudget est éditée à titre personnel par :
                    </p>

                    <ul>
                        <li><strong>Statut :</strong> Particulier</li>
                        <li><strong>Localisation :</strong> Lille, France</li>
                        <li><strong>Email :</strong> <a href="mailto:contact@home-budget.titerin.ovh">contact@home-budget.titerin.ovh</a>
                        </li>
                    </ul>

                    <h2>
                        Hébergement
                    </h2>

                    <p>
                        L’application est hébergée par :
                    </p>

                    <p>
                        <strong>OVHcloud</strong><br>
                        2 rue Kellermann<br>
                        59100 Roubaix<br>
                        France<br>
                        Site : <a href="https://www.ovhcloud.com" target="_blank">ovhcloud.com</a>
                    </p>

                    <h2>
                        Propriété intellectuelle
                    </h2>

                    <p>
                        L’ensemble des contenus présents sur l’application HomeBudget
                        (code, design, logo, textes) est protégé par le droit d’auteur.
                    </p>

                    <p>
                        Toute reproduction ou utilisation sans autorisation préalable est interdite.
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
