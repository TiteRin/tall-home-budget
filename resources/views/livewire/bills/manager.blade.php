<div>
    <h1 class="text-2xl font-bold mb-4 bg-primary-content/10 p-4 rounded-lg font-cursive text-center md:text-left">
        Gestion des charges du foyer
    </h1>

    <div class="hidden md:block">
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
                @if ($bill->id === $this->editingBillId && $this->isEditing === true)
                    @livewire('bills.bill-form', [
                        'householdMembers' => $this->householdMembers,
                        'hasJointAccount' => $this->hasHouseholdJointAccount,
                        'defaultDistributionMethod' => $this->defaultDistributionMethod,
                        'bill' => $bill
                    ])
                @else
                    @livewire('bills.row', ['bill' => $bill], key($bill->id))
                @endif
            @empty
                <tr>
                    <td colspan="6">
                        Aucune charge
                    </td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            @livewire('bills.bill-form', [
                'householdMembers' => $this->householdMembers,
                'hasJointAccount' => $this->hasHouseholdJointAccount,
                'defaultDistributionMethod' => $this->defaultDistributionMethod
            ])
            </tfoot>
        </table>
    </div>
    <div class="md:hidden flex flex-col gap-4">
        @foreach($bills as $bill)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col grow">
                            <p class="font-semibold font-cursive text-2xl">
                                {{ $bill->name }}
                            </p>
                            <p>
                                Payée par : {{ $bill->member->full_name }}
                            </p>
                            <p class="text-sm opacity-70">
                                Montant : {{ $bill->amount->toCurrency() }}
                            </p>
                            <p class="text-xs opacity-60">
                                Partage : {{ $bill->distribution_method->label() }}
                            </p>
                        </div>

                        <button
                            class="btn btn-ghost btn-sm"
                            wire:click="editBill({{ $bill->id }})">
                            Modifier
                        </button>
                    </div>

                </div>
            </div>

        @endforeach
    </div>
</div>
