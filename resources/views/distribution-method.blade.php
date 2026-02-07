@extends('layouts.app')

@section('content')
    <div
        class="relative min-h-screen bg-base-200 bg-center sm:flex sm:justify-center sm:items-center selection:bg-primary selection:text-primary-content">
        <div class="p-6 mx-auto max-w-7xl lg:p-8">
            <div class="flex flex-col items-end">
                <div class="flex flex-col items-center">
                    <h1 class="text-6xl font-bold text-base-content mb-4 cedarville-cursive-regular sr-only">
                        {{ config('app.name') }}
                    </h1>
                    <img src="/assets/img/logo.svg" alt="Logo de l'application" class="w-full h-48 mb-2">
                </div>
                <nav>
                    <a href="/" class="link link-primary">Retour à l’accueil</a>
                </nav>
            </div>

            <h2 class="font-cursive text-4xl mb-4">
                Les modes de partage au sein du foyer
            </h2>
            <p class="text-base-content/70">
                Plutôt 50/50 ou en fonction des revenus ? Plutôt compte joint, ou chacun paie de son côté ?
                Chaque foyer, chaque couple, chaque colocation a son propre moyen de fonctionner, et il n’y a pas de
                bonnes ou mauvaises méthodes, du moment que tout le monde est d’accord. <br/>
                Encore faut-il se poser la question.
            </p>
            <p>
                Car il n’y a pas que la contribution au foyer à prendre en compte dans une relation. En matière de
                finance, ce qui est important aussi ce sont <strong>le reste à vivre</strong> et <strong>la capacité
                    d’épargne</strong>. <br/>
                On ne vit pas que pour avoir un toit au dessus de la tête et du chauffage, et il y a les dépenses
                quotidiennes mais aussi le futur à penser, et à la sécurité financière. <br/>
            </p>

            <p>La question est donc : comment équilibrer une contribution juste et une capacité d’épargne ?</p>

            <h3 class="font-cursive text-2xl mt-8 mb-4">Le 50/50</h3>
            <p class="text-base-content/70">
                Ce mode de fonctionnement, que l’on peut aussi appelé <strong>partage égalitaire</strong> consiste en ce
                que chacun paie la même chose. <br/>
                À priori, la solution idéale, car après tout, dans la majorité des cas, on partage notre foyer de façon
                égale.
            </p>
            <p class="text-base-content/70">
                Les calculs sont simples, et peu importe de savoir combien Pierre, Paul ou Jacques gagnent, ce qui
                compte, c’est qu’à la fin, tout le monde ait réglé sa part.
            </p>
            <h4>Avantages</h4>
            <p class="text-base-content/70">
                C’est le mode le plus adapté aux <strong>colocations</strong>, aux <strong>couples qui ont des revenus
                    assez proches</strong> ou qui ne souhaitent pas se préoccuper de ce chacun gagne.
            </p>
            <h4>Inconvénients</h4>
            <p class="text-base-content/70">
                Il n’est pas le plus approprié quand il y a des <strong>différences de revenus</strong>, car le reste à
                vivre n’est pas le même pour chacun. S’il est plus égalitaire du point de vue des dépenses, il l’est
                beaucoup moins pour se constituer une épargne…
            </p>

            <h3 class="font-cursive text-2xl mt-8 mb-4">Le prorata</h3>
            <p class="text-base-content/70">
                Voilà le pendant du partage égalitaire, le <strong>partage équitable</strong>. Cette fois-ci, on a
                besoin de savoir qui gagne combien tous les mois. C’est à partir de cela que l’on va décider comment
                chacun va contribuer au foyer.
            </p>
            <h4>Avantages</h4>
            <p class="text-base-content/70">
                Un partage équitable est plus juste pour les couples qui ont de fortes disparités dans leurs revenus.
                L’idée derrière cela c’est que la personne qui gagne le plus va contribuer le plus au foyer. Et ainsi
                chacun aura un reste à vivre, et une capacité d’épargne, proportionnelle à ses revenus.
            </p>

            <h4>Inconvénients</h4>
            <p class="text-base-content/70">
                Ce n’est pas toujours perçu comme juste, selon comment on considère le foyer et la relation entre les
                personnes qui vivent ensemble. En cela, le partage au prorata n’est pas toujours adaptés aux colocations
                car cela peut créer des ressentiments ou des conflits basés sur la contribution. <br/>
                Le calcul est plus compliqué à mettre en place <strong>mais pas d’inquiétude, avec <span
                        class="font-cursive">apla</span>, on s’en occupe !</strong>
            </p>

            <h3 class="font-cursive text-2xl mt-8 mb-4">Le compte joint</h3>
            <p class="text-base-content/70">
                Ici, on parle de la méthode qui consiste à n’avoir qu’un compte joint pour l’ensemble des membres du
                foyer. Les revenus y sont versés dans leur intégralité, et toutes les dépenses, charges, factures
                tombent dessus. <br/>
                Ce mode de fonctionnement a longtemps été celui de nos parents et grands-parents, à une époque où les
                femmes ne touchaient pas de salaire mais étaient les gestionnaires du foyer.
            </p>
            <h4>Avantages</h4>
            <p class="text-base-content/70">
                Il n’y a pas plus simple. On ne se pose pas la question de qui paie combien, qui paie quoi. On considère
                le foyer comme une entité à part entière.
            </p>

            <h4>Inconvénients</h4>
            <p class="text-base-content/70">
                Aujourd’hui, c’est perçu comme une méthode assez désuète, voire dangereuse. Cela requiert une très
                grande confiance en l’autre, au quotidien (gestion des dépenses) comme sur le long terme (absence
                d’épargne). <br>
                Il y a toujours la possibilité d’avoir un compte joint pour les dépenses communes, et un compte
                personnel pour ses dépenses. Quant à savoir combien il faut mettre en commun, <strong
                    class="font-cursive">apla</strong> est là !
            </p>

            <h3 class="font-cursive text-2xl mt-8 mb-4">L’épargne à 50/50</h3>
            <p class="text-base-content/70">
                Sur la même idée de tout mettre sur le compte joint, ici, une fois toutes les charges passées, ce qu’il
                reste est partagé de façon égale entre les membres du foyer, sans considération de la contribution.
                Ainsi, chacun·e a exactement le même reste à vivre et la même capacité d’épargne indépendamment de ses
                revenus.
            </p>
            <h4>Avantages</h4>
            <p class="text-base-content/70">
                C’est particulièrement intéressants pour les couples qui ont des revenus irréguliers : plusieurs mois
                avec des revenus élevés, plusieurs mois avec des revenus faibles, etc. L’idée n’est pas d’équilibrer sur
                le long terme, mais de s’assurer que chacun ait de quoi vivre une fois les dépenses fixes passées.
            </p>

            <h4>Inconvénients</h4>
            <p class="text-base-content/70">
                À nouveau, cela demande une grande confiance et une grande transparence dans la relation. Les calculs
                peuvent être compliqués. Il n’est pas forcément adapté à la majorité des foyers. Mais c’est intéressant
                de savoir que cela existe !
            </p>

            <h3 class="font-cursive text-2xl mt-8 mb-4">Conclusion</h3>
            <p class="text-base-content/70">
                Ne faites pas un 50/50 par défaut, prenez le temps d’y réfléchir. Il n’y a pas de bonnes ou de mauvaises
                méthodes de partage, mais cela se discute, et il ne faut pas que l’argent soit un tabou.
                Si vous souhaitez en savoir plus, je vous invite à lire <a
                    href="https://www.babelio.com/livres/Lecoq-Le-Couple-et-lArgent/1440205" target="_blank"
                    class="link link-secondary">Titiou Lecoq, Le couple et l'argent</a>.
            </p>


        </div>
    </div>
@endsection
