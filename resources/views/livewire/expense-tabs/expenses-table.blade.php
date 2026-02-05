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
                <td colspan="5" class="text-center">
                    Aucune dépense
                </td>
            </tr>
        @endif
        <livewire:expense-tabs.expense-form
            :expense-tab-id="$expenseTabId"
            :household-members="\App\Models\ExpenseTab::find($expenseTabId)->household->members"
        />
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" class="text-right">Total du mois en cours</td>
            <td>
                {{ $totalAmount }}
            </td>
            <td></td>
        </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</div>
