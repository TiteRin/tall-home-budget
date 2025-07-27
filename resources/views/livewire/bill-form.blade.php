<tr>
    <td>
        <input type="text"
               wire:model.live="newName"
               placeholder="Nouvelle dépense"
               class="input input-bordered input-sm w-full"
        />
    </td>
    <td class="relative">
        <input type="text"
               wire:model.blur="formattedNewAmount"
               value="{{ $formattedNewAmount }}"
               placeholder="Montant"
               class="input input-bordered input-sm"
       />
    </td>
    <td>
        <select class="select select-bordered" wire:model.live="newDistributionMethod">
            @foreach ($this->distributionMethods as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="select select-bordered" wire:model.live="newMemberId">
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
