<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

describe('Logout', function () {
    test('an authenticated user can log out', function () {
        $user = User::factory()->create();
        $this->be($user);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        expect(Auth::check())->toBeFalse();
    });

    test('an unauthenticated user can not log out', function () {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        expect(Auth::check())->toBeFalse();
    });
});
