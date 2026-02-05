<div class="tabs tabs-lift tabs-xl" role="tablist">
    <label class="tab">
        <input type="radio" name="tabs-expense-tabs" wire:model.live="activeTab" value="new"/>
        Nouvel onglet de dépense
    </label>
    <div @class(['tab-content bg-base-100 border-base-300 p-6', 'hidden' => $activeTab !== 'new'])>
        <livewire:expense-tabs.expense-tab-form/>
    </div>

    @foreach ($this->expensesTabs as $tab)
        <label class="tab">
            <input type="radio" name="tabs-expense-tabs" wire:model.live="activeTab" value="{{ $tab->id }}"/>
            {{ $tab->name }}
        </label>
        <div @class(['tab-content bg-base-100 border-base-300 p-6', 'hidden' => (string)$activeTab !== (string)$tab->id])>
            <livewire:expense-tabs.expense-tab-form :currentExpenseTabId="$tab->id" wire:key="form-{{ $tab->id }}"/>
            <div class="divider">Mes dépenses</div>
            <livewire:expense-tabs.expenses-table :expenseTabId="$tab->id" wire:key="table-{{ $tab->id }}"/>
        </div>
    @endforeach
</div>
