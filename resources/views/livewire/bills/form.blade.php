<div class="flex flex-col gap-4">
    <div class="form-control">
        <label class="label"><span class="label-text">Nom de la charge</span></label>
        <input type="text"
               wire:model="newName"
               placeholder="Nouvelle charge"
               class="input input-bordered w-full"
        />
        @error('newName')
        <span class="text-error text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-control">
        <label class="label"><span class="label-text">Montant</span></label>
        <input type="text"
               wire:model.blur="formattedNewAmount"
               placeholder="Montant"
               class="input input-bordered w-full"
        />
        @error('newAmount')
        <span class="text-error text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-control">
        <label class="label"><span class="label-text">Méthode de distribution</span></label>
        <select class="select select-bordered w-full" wire:model="newDistributionMethod">
            @foreach ($this->distributionMethodOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @error('newDistributionMethod')
        <span class="text-error text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-control">
        <label class="label"><span class="label-text">Qui paie ?</span></label>
        <select class="select select-bordered w-full" wire:model="newMemberId">
            <option value="" selected hidden>Membre du foyer</option>
            @foreach ($this->householdMemberOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
            @if ($this->hasJointAccount)
                <option value="-1">Compte joint</option>
            @endif
        </select>
        @error('newMemberId')
        <span class="text-error text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="mt-4 flex flex-col gap-2">
        @if ($this->bill)
            <button class="btn btn-primary w-full" wire:click.prevent="saveBill" type="button">Sauvegarder</button>
            <button class="btn w-full" wire:click.prevent="cancelEdition" type="button">Annuler</button>
        @else
            <button class="btn btn-primary w-full" wire:click.prevent="addBill" type="button">Ajouter</button>
        @endif
    </div>
</div>
