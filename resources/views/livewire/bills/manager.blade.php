<div>
    <h1 class="text-2xl font-bold mb-4 bg-primary-content/10 p-4 rounded-lg font-cursive text-center md:text-left">
        Gestion des charges du foyer
    </h1>

    <div class="hidden md:block">
        <div class="flex justify-end mb-4">
            <button class="btn btn-primary" wire:click="create">
                Ajouter une charge
            </button>
        </div>
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
                @livewire('bills.row', ['bill' => $bill], key($bill->id))
            @empty
                <tr>
                    <td colspan="5">
                        Aucune charge
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="md:hidden flex flex-col gap-4">
        <div class="flex justify-center">
            <button class="btn btn-primary" wire:click="create">
                Ajouter une charge
            </button>
        </div>

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

    @if($isEditing || $isCreating)
        <div class="modal modal-open">
            <div class="modal-box relative">
                <button wire:click="cancelEditBill" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                <h3 class="text-lg font-bold mb-4 font-cursive">
                    {{ $isEditing ? 'Modifier la charge' : 'Nouvelle charge' }}
                </h3>

                @livewire('bills.bill-form', [
                    'householdMembers' => $this->householdMembers,
                    'hasJointAccount' => $this->hasHouseholdJointAccount,
                    'defaultDistributionMethod' => $this->defaultDistributionMethod,
                    'bill' => $isEditing ? $bills->firstWhere('id', $editingBillId) : null,
                ], key($isEditing ? 'edit-'.$editingBillId : 'create'))
            </div>
            <div class="modal-backdrop bg-black/50" wire:click="cancelEditBill"></div>
        </div>
    @endif
</div>
