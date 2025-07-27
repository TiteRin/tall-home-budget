<tr>
    <td>
        <input type="text"
               wire:model="newName"
               placeholder="Nouvelle dépense"
               class="input input-bordered input-sm w-full"
        />
    </td>
    <td>
        <input type="number"
               wire:model.number="newAmount"
               placeholder="Montant"
               class="input input-bordered input-sm w-full"
               min="0.00"
               step="0.01"
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
        <select class="select select-bordered" wire:model="newMemberId">
            @foreach ($this->householdMembers as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
            @if ($this->hasJointAccount)
                <option value="">Compte joint</option>
            @endif
        </select>
    </td>
    <td><button class="btn btn-primary w-full">Ajouter</button></td>
</tr>
