<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-base-200 p-4 rounded-lg shadow-sm border border-base-300">
        <h2 class="text-sm font-semibold mb-2 uppercase tracking-wide text-base-content/70">Pour bien commencer :</h2>
        <ul class="space-y-2">
            <li class="flex items-center">
                @if($household->onboarding_configured_household)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-2" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                    <span class="line-through text-base-content/50">Paramétrer votre foyer</span>
                @else
                    <div class="h-5 w-5 border-2 border-base-300 rounded mr-2"></div>
                    <a href="{{ route('household.settings') }}" class="link link-primary no-underline hover:underline">Paramétrer
                        votre foyer</a>
                @endif
            </li>
            <li class="flex items-center">
                @if($household->onboarding_added_bills)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success mr-2" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                    <span class="line-through text-base-content/50">Ajouter des dépenses</span>
                @else
                    <div class="h-5 w-5 border-2 border-base-300 rounded mr-2"></div>
                    <a href="{{ route('bills') }}" class="link link-primary no-underline hover:underline">Ajouter des
                        dépenses</a>
                @endif
            </li>
        </ul>
    </div>
</div>
