<div class="flex flex-col gap-4">
    <div class="w-full flex flex-col md:flex-row gap-4 ">
        @livewire('home.accounts-list', ['members' => $members])
        @livewire('home.movements.movements-list', ['incomes' => $incomes], key('movements-list'))
    </div>
    <div class="flex-col gap-4 hidden md:flex">
        @livewire('bills.bills-list', ['bills' => $bills, 'expenseTabs' => $expenseTabs, 'members' => $members])
        @livewire('home.general-information', ['household' => $household])
    </div>
</div>
