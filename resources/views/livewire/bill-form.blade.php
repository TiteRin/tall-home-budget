<tr>
    <form wire:submit.prevent="submit">
        <td>
            <input type="text"
                   wire:model="newName"
                   placeholder="Nouvelle dépense"
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
            <br/>
            {{ $newAmount }}

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
        </td>
        <td>
            <select class="select select-bordered" wire:model="newMemberId">
                @foreach ($this->householdMemberOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
                @if ($this->hasJointAccount)
                    <option value="">Compte joint</option>
                @endif
            </select>
        </td>
        <td>
            <button class="btn btn-primary w-full" wire:click="submit">Ajouter</button>
        </td>
    </form>
</tr>
