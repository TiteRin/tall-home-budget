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
        </div>
    </div>
</div>
