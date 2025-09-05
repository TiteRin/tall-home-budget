<div class="flex flex-col gap-4">
    <div class="w-full flex flex-row gap-4">
        @livewire('home.accounts-list')
        @livewire('home.bills-list')
    </div>

    @livewire('home.movements-list')

    @livewire('home.general-information', ['household' => $household])
</div>
