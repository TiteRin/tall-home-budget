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
        <tr>
            <td>
                <input type="text"
                       wire:model="newName"
                       placeholder="Nouvelle dépense"
                       class="input input-bordered input-sm w-full"
                />
            </td>
            <td>
                <input type="text"
                       wire:model="newAmount"
                       placeholder="Montant"
                       class="input input-bordered input-sm w-full"
                />
            </td>
            <td>
                <select class="select select-bordered" wire:model="newDistributionMethod">
                    @foreach ($this->distributionMethods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text"
                       wire:model="newMemberId"
                       placeholder="Qui ?"
                       class="input input-bordered input-sm w-full"
                />
            </td>
            <td><button class="btn btn-primary w-full">Ajouter</button></td>
        </tr>
        </tfoot>
    </table>
</div>
