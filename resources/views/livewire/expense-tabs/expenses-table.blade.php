<div
    x-data="{
        page: localStorage.getItem('expenses_table_page_{{ $expenseTabId }}') || 1
    }"
    x-init="
        if (page > 1) {
            $wire.gotoPage(page);
        }
    "
    @expenses-table-page-updated.window="
        if ($event.detail.tabId == {{ $expenseTabId }}) {
            localStorage.setItem('expenses_table_page_{{ $expenseTabId }}', $event.detail.page)
        }
    "
>
    <div class="hidden md:block">
        <table class="table table-zebra">
            <thead>
            <th>
                Date
            </th>
            <th>
                Dépense
            </th>
            <th>
                Mode de partage
            </th>
            <th>
                Compte
            </th>
            <th>
                Montant
            </th>
            <th>
                Actions
            </th>
            </thead>
            <tbody>
            @foreach($expenses as $expense)
                @if($editingExpenseId === $expense->id)
                    <livewire:expense-tabs.expense-form
                        :expense="$expense"
                        :expense-tab-id="$expenseTabId"
                        :household-members="\App\Models\ExpenseTab::find($expenseTabId)->household->members"
                        :is-table="true"
                        wire:key="edit-expense-{{ $expense->id }}"
                    />
                @else
                    <tr @class(['opacity-50' => !$expense->spent_on->between($currentPeriodStart, $currentPeriodEnd)])>
                        <td>{{ $expense->spent_on->format('d/m/Y') }}</td>
                        <td>{{ $expense->name }}</td>
                        <td>{{ $expense->distribution_method->label() }}</td>
                        <td>{{ $expense->member->full_name }}</td>
                        <td>{{ $expense->amount }}</td>
                        <td>
                            <button class="btn btn-ghost btn-xs" wire:click="editExpense({{ $expense->id }})">Modifier
                            </button>
                        </td>
                    </tr>
                @endif
            @endforeach
            @if ($expenses->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">
                        Aucune dépense
                    </td>
                </tr>
            @endif
            <livewire:expense-tabs.expense-form
                :expense-tab-id="$expenseTabId"
                :household-members="\App\Models\ExpenseTab::find($expenseTabId)->household->members"
                :is-table="true"
            />
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">Total du mois en cours</td>
                <td class="font-bold">
                    {{ $totalAmount }}
                </td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="md:hidden space-y-4">
        <div class="flex justify-between items-center bg-base-200 p-3 rounded-lg">
            <span class="font-bold">Total (mois en cours) : {{ $totalAmount }}</span>
            <button class="btn btn-primary btn-sm" wire:click="create">
                Ajouter
            </button>
        </div>

        @foreach($expenses as $expense)
            <div @class(['card bg-base-100 shadow-sm border border-base-200', 'opacity-50' => !$expense->spent_on->between($currentPeriodStart, $currentPeriodEnd)])>
                <div class="card-body p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm opacity-70">{{ $expense->spent_on->format('d/m/Y') }}</div>
                            <div class="font-bold text-lg">{{ $expense->name }}</div>
                            <div class="text-sm">{{ $expense->member->full_name }}</div>
                            <div class="text-xs opacity-60">{{ $expense->distribution_method->label() }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-lg text-primary">{{ $expense->amount }}</div>
                            <button class="btn btn-ghost btn-sm mt-2" wire:click="editExpense({{ $expense->id }})">
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($expenses->isEmpty())
            <div class="text-center p-8 bg-base-200 rounded-lg">
                Aucune dépense
            </div>
        @endif
    </div>

    @if($isCreating || $editingExpenseId)
        <div class="modal modal-open">
            <div class="modal-box relative">
                <button wire:click="stopEditing" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                <h3 class="text-lg font-bold mb-4 font-cursive">
                    {{ $editingExpenseId ? 'Modifier la dépense' : 'Nouvelle dépense' }}
                </h3>

                <livewire:expense-tabs.expense-form
                    :expense="$editingExpenseId ? $expenses->firstWhere('id', $editingExpenseId) : null"
                    :expense-tab-id="$expenseTabId"
                    :household-members="\App\Models\ExpenseTab::find($expenseTabId)->household->members"
                    wire:key="{{ $editingExpenseId ? 'edit-expense-modal-'.$editingExpenseId : 'create-expense-modal' }}"
                />
            </div>
            <div class="modal-backdrop bg-black/50" wire:click="stopEditing"></div>
        </div>
    @endif

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</div>
