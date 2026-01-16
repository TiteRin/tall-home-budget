<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <h3 class="card-title">Foyer "{{ $householdName }}"</h3>

        <div class="space-y-6">
            {{-- Message flash --}}
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Configuration du foyer --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Configuration</h3>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nom du foyer</span>
                    </label>
                    <input type="text" wire:model="householdName" class="input input-bordered">
                    @error('household.name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Compte joint</span>
                    </label>
                    <select class="select select-bordered" wire:model.boolean="hasJointAccount">
                        <option value="true">Oui</option>
                        <option value="false">Non</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Méthode de distribution par défaut</span>
                    </label>
                    <select class="select select-bordered" wire:model="defaultDistributionMethod">
                        @foreach ($this->distributionMethodOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-actions justify-end">
                    <button class="btn btn-primary" wire:click="save">Enregistrer</button>
                </div>
            </div>

            {{-- Membres existants --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Membres du foyer</h3>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Compte</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($householdMembers as $index => $member)
                            <tr>
                                @if($editingMemberId === $member['id'])
                                    <td>
                                        <input type="text" wire:model="editingMemberFirstName"
                                               class="input input-bordered input-sm w-full">
                                        @error('editingMemberFirstName') <span
                                            class="text-error text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <input type="text" wire:model="editingMemberLastName"
                                               class="input input-bordered input-sm w-full">
                                        @error('editingMemberLastName') <span
                                            class="text-error text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <div class="badge badge-info">Édition en cours</div>
                                    </td>
                                    <td>
                                        <div class="flex gap-1">
                                            <button wire:click="updateMember" class="btn btn-success btn-sm">
                                                Enregistrer
                                            </button>
                                            <button wire:click="cancelEdit" class="btn btn-ghost btn-sm">Annuler
                                            </button>
                                        </div>
                                    </td>
                                @else
                                    <td>{{ $member['first_name'] ?? '' }}</td>
                                    <td>{{ $member['last_name'] ?? '' }}</td>
                                    <td>
                                        @if (isset($member['user']))
                                            <div class="badge badge-success">Actif</div>
                                        @else
                                            <button class="btn btn-sm btn-outline btn-primary"
                                                    onclick="navigator.clipboard.writeText('{{ $this->getInviteLink($member['id']) }}'); alert('Lien copié !')">
                                                Inviter
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex gap-1">
                                            @if (!isset($member['user']))
                                                <button wire:click="editMember({{ $index }})"
                                                        class="btn btn-info btn-sm">
                                                    Modifier
                                                </button>
                                            @endif
                                            <button wire:click="removeMember({{ $index }})"
                                                    class="btn btn-error btn-sm">
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Aucun membre dans le foyer</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td>
                                <input type="text" wire:model="newMemberFirstName" placeholder="Prénom"
                                       class="input input-bordered input-sm w-full">
                            </td>
                            <td>
                                <input type="text" wire:model="newMemberLastName" placeholder="Nom"
                                       class="input input-bordered input-sm w-full">
                            </td>
                            <td>
                                <button wire:click="addMember" class="btn btn-primary btn-sm">
                                    Ajouter
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
