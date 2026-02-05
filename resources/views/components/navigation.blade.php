<div class="flex justify-between items-center w-full">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold truncate cedarville-cursive-regular px-1">
        <a href="{{ route('home') }}">
            {{ config('app.name') }}
        </a>
    </h1>

    @auth
        <!-- Menu déroulant -->
        <details class="dropdown dropdown-end" x-data="{ open: false }">
            <summary class="btn btn-ghost btn-sm sm:btn-md" @click="open = !open" @click.away="open = false">
                Menu
            </summary>

            <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-48 sm:w-52 text-sm sm:text-base"
                x-show="open">
                <li>
                    <a href="{{ route('household.settings') }}">Paramétrage</a>
                </li>
                <li>
                    <a href="{{ route('bills') }}">Charges</a>
                </li>
                <li>
                    <a href="{{ route('expense-tabs.index') }}">Dépenses ponctuelles</a>
                </li>
                <li>
                    <a href="{{ route('profile') }}">Mon profil</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="submit" class="w-full text-left">Déconnexion</button>
                    </form>
                </li>
            </ul>
        </details>
    @endauth


</div>
