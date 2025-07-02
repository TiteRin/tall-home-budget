<div>
    <h1>Dépenses du foyer</h1>
    <table class="table">
        <thead>
        <tr>
            <td>Nom</td>
            <td>Montant</td>
            <td>Méthode de distribution</td>
            <td>Qui paie ?</td>
            <td>Actions</td>
        </tr>
        </thead>
        <tbody>
        @forelse($bills as $index => $bill)
            <tr>
                <td>{{ $bill->name ?? '' }}</td>
                <td>{{ $bill->formatted_amount ?? '-' }}</td>
                <td>{{ $bill->distribution_method->label() ?? '' }}</td>
                <td>{{ $bill->member ? $bill->member->fullname : '<em>Compte joint</em>' }}</td>
                <td>Actions</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    Aucune dépense
                </td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        @livewire('bill-form', [
            'distributionMethods' => $this->distributionMethods,
            'householdMembers' => $this->householdMembers
        ])
        </tfoot>
    </table>
</div>
