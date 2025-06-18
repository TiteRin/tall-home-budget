<x-app-layout title="Accueil">
    <div class="space-y-6">
        @if($household)
        <h1 class="text-3xl font-bold text-base-content">
            Foyer {{ $household->name }}
        </h1>
        <section class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    Informations générales
                </h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 mt-2">
                    <div class="flex items-center gap-3">
                        <div class="text-primary">
                            <x-heroicon-o-scale class="h-6 w-6" />
                        </div>
                        <div>
                        <dt class="text-sm sm:text-base-content/70">Mode de répartition par défaut</dt>
                        <dd class="text-base font-semibold">{{ $household->getDefaultDistributionMethod()->label() }}</dd>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-primary">
                            <x-heroicon-o-building-library class="h-6 w-6" />
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

        <section class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    Membres du foyer
                </h2>
            </div>
            <ul class="list rounded-box shadow-md">
                @foreach($household->members as $member)
                <li class="list-row flex items-center gap-3">
                    <div class="text-primary">
                        <x-heroicon-o-user class="h-6 w-6" />
                    </div>
                    <div>
                        <span class="text-sm sm:text-base-content/70">
                            {{ $member->full_name }}
                        </span>
                    </div>
                </li>
                @endforeach
            </ul>
        </section>
        @else
        <div class="alert alert-info">
            <span>No data available.</span>
        </div>
        @endif
    </div>
</x-app-layout>