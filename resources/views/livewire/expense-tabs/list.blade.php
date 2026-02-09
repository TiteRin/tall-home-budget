<div>
    <h1 class="text-2xl font-bold mb-4 bg-primary-content/10 p-4 rounded-lg font-cursive text-center md:text-left">
        Dépenses ponctuelles
    </h1>
    <div class="hidden md:flex tabs tabs-lift tabs-xl" role="tablist">
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

    <div class="md:hidden space-y-2">
        <div class="collapse collapse-arrow bg-base-100 border border-base-300">
            <input type="radio" name="mobile-expense-tabs" wire:model.live="activeTab" value="new"/>
            <div class="collapse-title text-xl font-medium">
                Nouvel onglet de dépense
            </div>
            <div class="collapse-content">
                <livewire:expense-tabs.expense-tab-form/>
            </div>
        </div>

        @foreach ($this->expensesTabs as $tab)
            <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                <input type="radio" name="mobile-expense-tabs" wire:model.live="activeTab" value="{{ $tab->id }}"/>
                <div class="collapse-title text-xl font-medium">
                    {{ $tab->name }}
                </div>
                <div class="collapse-content">
                    <livewire:expense-tabs.expense-tab-form :currentExpenseTabId="$tab->id"
                                                            wire:key="mobile-form-{{ $tab->id }}"/>
                    <div class="divider text-sm">Mes dépenses</div>
                    <livewire:expense-tabs.expenses-table :expenseTabId="$tab->id"
                                                          wire:key="mobile-table-{{ $tab->id }}"/>
                </div>
            </div>
        @endforeach
    </div>
</div>
