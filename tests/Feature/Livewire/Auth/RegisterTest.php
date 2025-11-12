<?php

namespace Tests\Feature\Livewire\Auth;

use App\Enums\DistributionMethod;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

describe('Register component', function () {

    test('register page is accessible', function () {
        $response = $this->get('/register');
        $response->assertStatus(200);
    });

    test('a user can register with household', function () {
        Event::fake();

        Livewire::test(Register::class)
            ->set('firstName', 'John')
            ->set('lastName', 'Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123!')
            ->set('passwordConfirmation', 'password123!')
            ->set('householdName', 'Doe Family')
            ->set('defaultDistributionMethod', DistributionMethod::EQUAL->value)
            ->set('hasJointAccount', false)
            ->call('register')
            ->assertRedirect(route('login'));

        $this->assertTrue(User::whereEmail('john@example.com')->exists());

        $user = User::whereEmail('john@example.com')->first();
        expect($user->member->first_name)->toBe('John')
            ->and($user->member->last_name)->toBe('Doe')
            ->and($user->member->household->name)->toBe('Doe Family');

        Event::assertDispatched(Registered::class);
    });
});
