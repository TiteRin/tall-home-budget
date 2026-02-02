<div class="flex flex-col gap-4">
    <div class="w-full flex flex-row gap-4">
        @livewire('home.accounts-list', ['members' => $members])
        @livewire('bills.bills-list', ['bills' => $bills, 'expenseTabs' => $expenseTabs])
    </div>

    @livewire('home.movements.movements-list', [
        'incomes' => $incomes
    ])

    @livewire('home.general-information', ['household' => $household])
</div>
