<div>
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
            <tr @class(['opacity-50' => !$expense->spent_on->between($currentPeriodStart, $currentPeriodEnd)])>
                <td>{{ $expense->spent_on->format('d/m/Y') }}</td>
                <td>{{ $expense->name }}</td>
                <td>{{ $expense->distribution_method->label() }}</td>
                <td>{{ $expense->member->full_name }}</td>
                <td>{{ $expense->amount }}</td>
                <td></td>
            </tr>
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
