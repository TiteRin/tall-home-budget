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
                                            @if (!isset($member['user']) || $member['id'] === auth()->user()->member_id)
                                                <button wire:click="editMember({{ $index }})"
                                                        class="btn btn-info btn-sm">
                                                    Modifier
                                                </button>
                                            @endif

                                            @if (!isset($member['user']))
                                                <button wire:click="removeMember({{ $index }})"
                                                        class="btn btn-error btn-sm">
                                                    Supprimer
                                                </button>
                                            @else
                                                <div class="tooltip"
                                                     data-tip="Impossible de supprimer un membre ayant un compte actif">
                                                    <button class="btn btn-error btn-sm" disabled>
                                                        Supprimer
                                                    </button>
                                                </div>
                                            @endif
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

            {{-- Profil Utilisateur --}}
            <div class="pt-6 border-t border-base-300 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold">Mes informations personnelles</h3>
                    <p class="text-sm text-base-content/70">Gérez votre adresse e-mail et votre mot de passe.</p>
                </div>
                <a href="{{ route('profile') }}" class="btn btn-outline">
                    Modifier mon profil
                </a>
            </div>
        </div>
    </div>

    {{-- Modale de confirmation de suppression --}}
    <input type="checkbox" id="confirm-delete-member" class="modal-toggle" @if($memberIdToDelete) checked @endif />
    <div class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirmer la suppression</h3>
            <p class="py-4">Êtes-vous sûr de vouloir supprimer ce membre ?</p>

            @if($impactedBillsCount > 0)
                <div class="alert alert-warning shadow-sm mb-4 py-2">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 flex-shrink-0"/>
                        <span>Ce membre a <strong>{{ $impactedBillsCount }}</strong> {{ $impactedBillsCount > 1 ? 'charges associées' : 'dépense associée' }}.</span>
                    </div>
                </div>

                <div class="form-control mb-4">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" wire:model="deleteAction" value="reassign" class="radio radio-primary"/>
                        <span
                            class="label-text">Réaffecter les charges au <strong>{{ $reassignmentTarget }}</strong></span>
                    </label>
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" wire:model="deleteAction" value="delete_bills" class="radio radio-error"/>
                        <span class="label-text">Supprimer également toutes les charges associées</span>
                    </label>
                </div>
            @endif

            <div class="modal-action">
                <button wire:click="$set('memberIdToDelete', null)" class="btn btn-ghost">Annuler</button>
                <button wire:click="performDelete" class="btn btn-error">Supprimer définitivement</button>
            </div>
        </div>
        <label class="modal-backdrop" wire:click="$set('memberIdToDelete', null)">Fermer</label>
    </div>
</div>
