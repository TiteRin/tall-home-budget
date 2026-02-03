<div class="flex flex-col gap-4">
    <div class="w-full flex flex-row gap-4">
        @livewire('home.accounts-list', ['members' => $members])
        @livewire('home.movements.movements-list', ['incomes' => $incomes], key('movements-list'))
    </div>

    @livewire('bills.bills-list', ['bills' => $bills, 'expenseTabs' => $expenseTabs])
    @livewire('home.general-information', ['household' => $household])
</div>
