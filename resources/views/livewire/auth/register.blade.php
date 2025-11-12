<div class="min-h-screen flex items-center justify-center bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold">
                Créer un compte
            </h2>
        </div>

        <form wire:submit="register" class="mt-8 space-y-6">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body space-y-4">
                    <!-- Informations personnelles -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Vos informations</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Prénom</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="firstName"
                                    class="input input-bordered @error('firstName') input-error @enderror"
                                    placeholder="John"
                                />
                                @error('firstName')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="lastName"
                                    class="input input-bordered @error('lastName') input-error @enderror"
                                    placeholder="Doe"
                                />
                                @error('lastName')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email</span>
                            </label>
                            <input
                                type="email"
                                wire:model="email"
                                class="input input-bordered @error('email') input-error @enderror"
                                placeholder="john@example.com"
                            />
                            @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Mot de passe</span>
                                </label>
                                <input
                                    type="password"
                                    wire:model="password"
                                    class="input input-bordered @error('password') input-error @enderror"
                                    placeholder="••••••••"
                                />
                                @error('password')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Confirmer le mot de passe</span>
                                </label>
                                <input
                                    type="password"
                                    wire:model="passwordConfirmation"
                                    class="input input-bordered"
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <!-- Informations du foyer -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Votre foyer</h3>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nom du foyer</span>
                            </label>
                            <input
                                type="text"
                                wire:model="householdName"
                                class="input input-bordered @error('householdName') input-error @enderror"
                                placeholder="Famille Doe"
                            />
                            @error('householdName')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Méthode de calcul par défaut</span>
                            </label>
                            <select
                                wire:model="defaultDistributionMethod"
                                class="select select-bordered @error('defaultDistributionMethod') select-error @enderror"
                            >
                                @foreach($distributionMethods as $method)
                                    <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                @endforeach
                            </select>
                            @error('defaultDistributionMethod')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Compte joint</span>
                                <input
                                    type="checkbox"
                                    wire:model="hasJointAccount"
                                    class="checkbox"
                                />
                            </label>
                        </div>
                    </div>

                    <div class="card-actions justify-end mt-6">
                        <button type="submit" class="btn btn-primary">
                            S'inscrire
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" wire:navigate class="link link-primary">
                            Déjà un compte ? Se connecter
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
