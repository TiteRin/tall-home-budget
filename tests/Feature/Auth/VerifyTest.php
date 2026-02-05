<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

describe('Email Verification', function () {

    it('can view verification page', function () {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Auth::login($user);

        $this->get(route('verification.notice'))
            ->assertSuccessful()
            ->assertSeeLivewire('auth.verify');
    });

    it('can resend verification email', function () {
        $user = User::factory()->create();

        Livewire::actingAs($user);

        Livewire::test('auth.verify')
            ->call('resend')
            ->assertDispatched('resent');
    });

    it('can verify email', function () {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Auth::login($user);

        $url = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)
            ->assertRedirect(route('home'));

        $this->assertTrue($user->hasVerifiedEmail());
    });

    it('throws authorization exception if id does not match', function () {
        $user = User::factory()->create(['email_verified_at' => null]);
        $otherUser = User::factory()->create();

        Auth::login($user);

        $url = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(60), [
            'id' => $otherUser->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)->assertStatus(403);
    });

    it('throws authorization exception if hash does not match', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        Auth::login($user);

        $url = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => 'invalid-hash',
        ]);

        $this->get($url)->assertStatus(403);
    });

    it('redirects to home if already verified', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        $url = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)->assertRedirect(route('home'));
    });
});
