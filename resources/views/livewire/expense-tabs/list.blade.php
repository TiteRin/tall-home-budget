<div class="tabs tabs-lift tabs-xl" role="tablist">
    <label class="tab">
        <input type="radio" name="tabs-expense-tabs" checked/>
        Nouvel onglet de dépense
    </label>
    <div class="tab-content bg-base-100 border-base-300 p-6">
        <livewire:expense-tabs.expense-tab-form/>
    </div>

    @foreach ($expenseTabs as $tab)
        <label class="tab">
            <input type="radio" name="tabs-expense-tabs"/>
            {{ $tab->name }}
        </label>
        <div class="tab-content bg-base-100 border-base-300 p-6">
            <livewire:expense-tabs.expense-tab-form :currentExpenseTabId="$tab->id"/>
        </div>
    @endforeach
</div>
