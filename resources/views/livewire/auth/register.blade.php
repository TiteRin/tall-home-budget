<div class="min-h-screen flex items-center justify-center bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold">
                Créer un compte
            </h2>
            <p class="mt-2 text-center text-sm text-base-content/60">
                Rejoignez Home Budget pour gérer votre foyer
            </p>
        </div>

        <form wire:submit="register" class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-6">

                {{-- Informations personnelles --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold">Vos informations</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label" for="firstName">
                                <span class="label-text">Prénom</span>
                            </label>
                            <input
                                type="text"
                                id="firstName"
                                wire:model="firstName"
                                class="input input-bordered @error('firstName') input-error @enderror"
                                required
                            />
                            @error('firstName')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label" for="lastName">
                                <span class="label-text">Nom</span>
                            </label>
                            <input
                                type="text"
                                id="lastName"
                                wire:model="lastName"
                                class="input input-bordered @error('lastName') input-error @enderror"
                                required
                            />
                            @error('lastName')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold">Vos identifiants</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control md:col-span-2">
                            <label class="label" for="email">
                                <span class="label-text">Email</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                wire:model="email"
                                class="w-full input input-bordered @error('email') input-error @enderror"
                                required
                            />
                            @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label" for="password">
                                <span class="label-text">Mot de passe</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                wire:model="password"
                                class="input input-bordered @error('password') input-error @enderror"
                                required
                            />
                            @error('password')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label" for="passwordConfirmation">
                                <span class="label-text">Confirmer le mot de passe</span>
                            </label>
                            <input
                                type="password"
                                id="passwordConfirmation"
                                wire:model="passwordConfirmation"
                                class="input input-bordered @error('passwordConfirmation') input-error @enderror"
                                required
                            />
                            @error('passwordConfirmation')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Informations du foyer --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold">Votre foyer</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label" for="householdName">
                                <span class="label-text">Nom du foyer</span>
                            </label>
                            <input
                                type="text"
                                id="householdName"
                                wire:model="householdName"
                                class="input input-bordered @error('householdName') input-error @enderror"
                                placeholder="Ex: Famille Dupont"
                                required
                            />
                            @error('householdName')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label" for="defaultDistributionMethod">
                                <span class="label-text">Méthode de calcul par défaut</span>
                            </label>
                            <select
                                id="defaultDistributionMethod"
                                wire:model="defaultDistributionMethod"
                                class="select select-bordered @error('defaultDistributionMethod') select-error @enderror"
                                required
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
                            <label class="label cursor-pointer justify-start gap-4">
                                <input
                                    type="checkbox"
                                    wire:model="hasJointAccount"
                                    class="checkbox"
                                />
                                <span class="label-text">Le foyer possède un compte joint</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="space-y-4">
                    <button type="submit" class="btn btn-primary w-full">
                        Créer mon compte
                    </button>

                    <p class="text-center text-sm">
                        Vous avez déjà un compte ?
                        <a href="{{ route('login') }}" wire:navigate class="link link-primary">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
