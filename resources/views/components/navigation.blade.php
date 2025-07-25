<div class="flex justify-between items-center w-full">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold truncate">
        <a href="{{ route('home') }}">
            Home Budget
        </a>
    </h1>

    <!-- Menu déroulant -->
    <details class="dropdown dropdown-end" x-data="{ open: false }">
        <summary class="btn btn-ghost btn-sm sm:btn-md" @click="open = !open" @click.away="open = false">
            Menu
        </summary>

        <ul class="dropdown-content menu bg-base-100 rounded-box z-1 w-48 sm:w-52 text-sm sm:text-base" x-show="open">
            <li>
                <a href="{{ route('household.settings') }}">Paramétrage</a>
            </li>
            <li>
                <a href="{{ route('bills.settings') }}">Dépenses</a>
            </li>
        </ul>
    </details>

    
</div>