@if(isset($isTable) && $isTable)
    <tr>
        <td>
            <input type="text"
                   wire:model="newName"
                   placeholder="Dépense"
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
            <input type="date"
                   wire:model="newSpentOn"
                   class="input input-bordered input-sm"
            />
            @error('newSpentOn')
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
            </select>

            @error('newMemberId')
            <br/>
            <span class="text-error text-sm">
            {{ $message  }}
        </span>
            @enderror
        </td>
        <td>
            @if ($this->expense)
                <div class="flex flex-col gap-1">
                    <button class="btn btn-primary btn-sm" wire:click.prevent="saveExpense" type="button">Sauvegarder
                    </button>
                    <button class="btn btn-error btn-sm"
                            wire:confirm="Êtes-vous sûr de vouloir supprimer cette dépense ?"
                            wire:click.prevent="deleteExpense"
                            type="button">Supprimer
                    </button>
                    <button class="btn btn-sm" wire:click.prevent="cancelEdition" type="button">Annuler</button>
                </div>
            @else
                <button class="btn btn-primary w-full" wire:click.prevent="addExpense" type="button">Ajouter</button>
            @endif
        </td>
    </tr>
@else
    <div class="flex flex-col gap-4">
        <div class="form-control">
            <label class="label"><span class="label-text">Nom de la dépense</span></label>
            <input type="text"
                   wire:model="newName"
                   placeholder="Dépense"
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
            <label class="label"><span class="label-text">Date</span></label>
            <input type="date"
                   wire:model="newSpentOn"
                   class="input input-bordered w-full"
            />
            @error('newSpentOn')
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
            </select>
            @error('newMemberId')
            <span class="text-error text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-4 flex flex-col gap-2">
            @if ($this->expense)
                <button class="btn btn-primary w-full" wire:click.prevent="saveExpense" type="button">Sauvegarder
                </button>
                <button class="btn btn-error w-full"
                        wire:confirm="Êtes-vous sûr de vouloir supprimer cette dépense ?"
                        wire:click.prevent="deleteExpense"
                        type="button">Supprimer
                </button>
                <button class="btn w-full" wire:click.prevent="cancelEdition" type="button">Annuler</button>
            @else
                <button class="btn btn-primary w-full" wire:click.prevent="addExpense" type="button">Ajouter</button>
            @endif
        </div>
    </div>
@endif
