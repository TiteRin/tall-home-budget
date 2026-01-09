@section('title', 'Sign in to your account')

<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}">
            <x-logo class="w-auto h-16 mx-auto text-primary"/>
        </a>

        <h2 class="mt-6 text-3xl font-extrabold text-center leading-9">
            Sign in to your account
        </h2>
        @if (Route::has('register'))
            <p class="mt-2 text-sm text-center text-base-content/60 leading-5 max-w">
                Or
                <a href="{{ route('register') }}"
                   class="font-medium text-primary hover:text-primary-focus focus:outline-none focus:underline transition ease-in-out duration-150">
                    create a new account
                </a>
            </p>
        @endif
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form wire:submit.prevent="authenticate">
                    <div class="form-control">
                        <label for="email" class="label">
                            <span class="label-text">Email address</span>
                        </label>

                        <input wire:model.lazy="email" id="email" name="email" type="email" required autofocus
                               class="input input-bordered w-full @error('email') input-error @enderror"/>

                        @error('email')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="form-control mt-4">
                        <label for="password" class="label">
                            <span class="label-text">Password</span>
                        </label>

                        <input wire:model.lazy="password" id="password" type="password" required
                               class="input input-bordered w-full @error('password') input-error @enderror"/>

                        @error('password')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <div class="flex items-center">
                            <input wire:model.lazy="remember" id="remember" type="checkbox"
                                   class="checkbox checkbox-primary checkbox-sm"/>
                            <label for="remember" class="ml-2 label cursor-pointer">
                                <span class="label-text">Remember</span>
                            </label>
                        </div>

                        <div class="text-sm leading-5">
                            <a href="{{ route('password.request') }}"
                               class="link link-primary font-medium transition ease-in-out duration-150">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary w-full">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
