<tr>
    <td>
        <input type="text"
               wire:model="newName"
               placeholder="Nouvelle charge"
               class="input input-bordered input-sm w-full"
        />
        @error('newName')
        <br/>
        <span class="text-error text-sm">
                    {{ $message  }}
                </span>
        @enderror
    </td>
    <td class="relative">
        <input type="text"
               wire:model.blur="formattedNewAmount"
               value="{{ $formattedNewAmount }}"
               placeholder="Montant"
               class="input input-bordered input-sm"
        />
        @error('newAmount')
        <br/>
        <span class="text-error text-sm">
            {{ $message  }}
        </span>
        @enderror
    </td>
    <td>
        <select class="select select-bordered" wire:model="newDistributionMethod">
            @foreach ($this->distributionMethodOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @error('newDistributionMethod')
        <br/>
        <span class="text-error text-sm">
            {{ $message  }}
        </span>
        @enderror

    </td>
    <td>
        <select class="select select-bordered" wire:model="newMemberId">
            <option value="" selected hidden>Membre du foyer</option>
            @foreach ($this->householdMemberOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
            @if ($this->hasJointAccount)
                <option value="-1">Compte joint</option>
            @endif
        </select>

        @error('newMemberId')
        <br/>
        <span class="text-error text-sm">
            {{ $message  }}
        </span>
        @enderror
    </td>
    <td>
        @if ($this->bill)
            <button class="btn btn-primary w-full" wire:click.prevent="saveBill" type="button">Sauvegarder</button>
            <button class="btn w-full" wire:click.prevent="cancelEdition" type="button">Annuler</button>
        @else
            <button class="btn btn-primary w-full" wire:click.prevent="addBill" type="button">Ajouter</button>
        @endif
    </td>
</tr>
