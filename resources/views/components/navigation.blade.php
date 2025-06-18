<div class="flex justify-between items-center">
    <h1 class="text-2xl font-bold">Mon Foyer</h1>

    <!-- Menu déroulant -->
    <details class="dropdown dropdown-end" x-data="{ open: false }">
        <summary class="btn btn-ghost" @click="open = !open" @click.away="open = false">
            Menu
    </summary>

        <ul class="dropdown-content menu bg-base-100 rounded-box z-1" x-show="open">
            <li>
                <a href="{{ route('household.settings') }}">Paramétrage</a>
            </li>
            <li>
                <a href="{{ route('bills.settings') }}">Dépenses</a>
            </li>
        </ul>
    </details>

    
</div>