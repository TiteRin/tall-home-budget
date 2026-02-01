<table class="table">
    <thead>
    <th>
        Dépense
    </th>
    <th>
        Montant
    </th>
    <th>
        Date
    </th>
    <th>
        Mode de partage
    </th>
    <th>
        Compte
    </th>
    </thead>
    <tbody>
    @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->name }}</td>
            <td>{{ $expense->amount }}</td>
            <td>{{ $expense->spent_on->format('d/m/Y') }}</td>
            <td>{{ $expense->distribution_method->label() }}</td>
            <td>{{ $expense->member->full_name }}</td>
        </tr>
    @endforeach
    @if ($expenses->isEmpty())
        <tr>
            <td colspan="5" class="text-center">
                Aucune dépense
            </td>
        </tr>
    @endif
    </tbody>
    <tfoot>
    <livewire:expense-tabs.expense-form
        :expense-tab-id="$expenseTabId"
        :household-members="\App\Models\ExpenseTab::find($expenseTabId)->household->members"
    />
    </tfoot>
</table>
