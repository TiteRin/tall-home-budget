<x-app-layout title="Accueil">
    <h1 class="text-3xl font-bold text-base-content mb-4">
        Foyer {{ $household->name }}
    </h1>

    <div class="flex flex-col gap-4">
        <div class="w-full flex flex-row gap-4">
            @livewire('home.accounts-list')
            <section class="card bg-base-100 shadow-xl grow w-1/3">
                <div class="card-body">
                    <h2 class="card-title">
                        Dépenses
                    </h2>
                </div>
            </section>
        </div>
        <section class="card bg-base-100 shadow-xl w-full">
            <div class="card-body">
                <h2 class="card-title">
                    Mouvements
                </h2>
            </div>
        </section>

        <section class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    Informations générales
                </h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 mt-2">
                    <div class="flex items-center gap-3">
                        <div class="text-primary">
                            <x-heroicon-o-scale class="h-6 w-6"/>
                        </div>
                        <div>
                            <dt class="text-sm sm:text-base-content/70">Mode de répartition par défaut</dt>
                            <dd class="text-base font-semibold">{{ $household->getDefaultDistributionMethod()->label() }}</dd>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-primary">
                            <x-heroicon-o-building-library class="h-6 w-6"/>
                        </div>
                        <div>
                            <dt class="text-sm sm:text-base-content/70">Compte joint</dt>
                            <dd class="text-base font-semibold">
                                {{ $household->has_joint_account ? 'Oui' : 'Non' }}
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>
        </section>
    </div>
</x-app-layout>
