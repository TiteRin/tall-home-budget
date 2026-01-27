<div class="card bg-base-100 shadow-xl max-w-2xl mx-auto">
    <div class="card-body">
        <h3 class="card-title text-2xl mb-6">Mes informations personnelles</h3>

        <div class="space-y-8">
            {{-- Section Email --}}
            <section>
                <h4 class="text-lg font-semibold mb-4 border-b pb-2">Adresse e-mail</h4>
                @if (session()->has('email_message'))
                    <div class="alert alert-success py-2 mb-4">
                        {{ session('email_message') }}
                    </div>
                @endif
                <form wire:submit="updateEmail" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label class="label md:justify-end">
                            <span class="label-text font-medium">E-mail</span>
                        </label>
                        <div class="md:col-span-2">
                            <input type="email" wire:model="email" class="input input-bordered w-full">
                            @error('email') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="btn btn-primary">Mettre à jour l'e-mail</button>
                    </div>
                </form>
            </section>

            {{-- Section Mot de passe --}}
            <section>
                <h4 class="text-lg font-semibold mb-4 border-b pb-2">Changer le mot de passe</h4>
                @if (session()->has('password_message'))
                    <div class="alert alert-success py-2 mb-4">
                        {{ session('password_message') }}
                    </div>
                @endif
                <form wire:submit="updatePassword" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label class="label md:justify-end">
                            <span class="label-text font-medium">Mot de passe actuel</span>
                        </label>
                        <div class="md:col-span-2">
                            <input type="password" wire:model="current_password" class="input input-bordered w-full">
                            @error('current_password') <span
                                class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label class="label md:justify-end">
                            <span class="label-text font-medium">Nouveau mot de passe</span>
                        </label>
                        <div class="md:col-span-2">
                            <input type="password" wire:model="password" class="input input-bordered w-full">
                            @error('password') <span
                                class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label class="label md:justify-end">
                            <span class="label-text font-medium">Confirmer le mot de passe</span>
                        </label>
                        <div class="md:col-span-2">
                            <input type="password" wire:model="password_confirmation"
                                   class="input input-bordered w-full">
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                    </div>
                </form>
            </section>

            {{-- Section Suppression de compte --}}
            <section class="pt-8 border-t border-error/20">
                <h4 class="text-lg font-semibold mb-4 text-error">Zone de danger</h4>
                <div class="bg-error/5 border border-error/20 rounded-lg p-4">
                    <p class="text-sm mb-4">
                        La suppression de votre compte est irréversible.
                        @if(\App\Models\User::whereHas('member', function($query) {
                            $query->where('household_id', Auth::user()->member->household_id);
                        })->count() === 1)
                            En tant que seul utilisateur du foyer, cela supprimera également le foyer, tous ses membres
                            et l'ensemble des dépenses associées.
                        @else
                            Cela supprimera uniquement votre accès utilisateur. Les autres membres du foyer conserveront
                            leur accès et les données du foyer seront préservées.
                        @endif
                    </p>
                    <button type="button" class="btn btn-error btn-outline" onclick="delete_account_modal.showModal()">
                        Supprimer mon compte
                    </button>
                </div>
            </section>
        </div>
    </div>

    {{-- Modal de confirmation --}}
    <dialog id="delete_account_modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-error">Confirmer la suppression</h3>
            <p class="py-4">
                Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.
                Veuillez saisir votre mot de passe pour confirmer.
            </p>

            <form wire:submit.prevent="deleteAccount">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Votre mot de passe</span>
                    </label>
                    <input type="password" wire:model="delete_confirm_password" class="input input-bordered w-full"
                           required>
                    @error('delete_confirm_password') <span
                        class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="modal-action">
                    <button type="button" class="btn" onclick="delete_account_modal.close()">Annuler</button>
                    <button type="submit" class="btn btn-error">Confirmer la suppression</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</div>
