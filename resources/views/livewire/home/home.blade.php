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

    @livewire('home.general-information', ['household' => $household])
</div>
