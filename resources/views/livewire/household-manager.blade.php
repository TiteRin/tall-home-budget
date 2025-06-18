<div class="card bg-base-100 shadow-xl w-96">
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
            <input type="text" 
                   wire:model="householdName"
                   class="input input-bordered">
            @error('household.name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Compte joint</span>
            </label>
            <select class="select select-bordered" wire:model="hasJointAccount">
                <option value="1">Oui</option>
                <option value="0">Non</option>
            </select>
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Méthode de distribution</span>
            </label>
            <select class="select select-bordered" wire:model="defaultDistributionMethod">
                @foreach($this->distributionMethods as $value => $label) 
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($householdMembers as $index => $member)
                        <tr>
                            <td>{{ $member['first_name'] ?? '' }}</td>
                            <td>{{ $member['last_name'] ?? '' }}</td>
                            <td>
                                <button wire:click="removeMember({{ $index }})" class="btn btn-error btn-sm">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Aucun membre dans le foyer</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td>
                            <input type="text" 
                                   wire:model="newMemberFirstName"
                                   placeholder="Prénom"
                                   class="input input-bordered input-sm w-full">
                        </td>
                        <td>
                            <input type="text"
                                   wire:model="newMemberLastName" 
                                   placeholder="Nom"
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