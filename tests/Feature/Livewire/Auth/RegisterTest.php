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

    describe('Validation', function () {

        beforeEach(function () {
            $this->component = Livewire::test(Register::class)
                ->set('firstName', 'John')
                ->set('lastName', 'Doe')
                ->set('email', 'john@example.com')
                ->set('password', 'password123!')
                ->set('passwordConfirmation', 'password123!')
                ->set('householdName', 'Doe Family')
                ->set('defaultDistributionMethod', DistributionMethod::EQUAL->value)
                ->set('hasJointAccount', false);
        });

        test('first name is required', function () {
            $this->component->set('firstName', '')
                ->call('register')
                ->assertHasErrors(['firstName' => 'required']);
        });

        test('last name is required', function () {
            $this->component->set('lastName', '')
                ->call('register')
                ->assertHasErrors(['lastName' => 'required']);
        });

        test('email is required', function () {
            $this->component->set('email', '')
                ->call('register')
                ->assertHasErrors(['email' => 'required']);
        });

        test('email must be a valid email address', function () {
            $this->component->set('email', 'invalid-email')
                ->call('register')
                ->assertHasErrors(['email' => 'email']);
        });

        test('email must be unique', function () {
            User::factory()->create(['email' => 'duplicate@example.com']);
            $this->component->set('email', 'duplicate@example.com')
                ->call('register')
                ->assertHasErrors(['email' => 'unique']);
        });

        test('password must be confirmed', function () {
            $this->component->set('password', 'password123!')
                ->set('passwordConfirmation', 'different')
                ->call('register')
                ->assertHasErrors(['password']);
        });

        test('password should be at least 8 characters', function () {
            $this->component->set('password', 'abc')
                ->call('register')
                ->assertHasErrors(['password' => 'min']);
        });

        test('household name is required', function () {
            $this->component->set('householdName', '')
                ->call('register')
                ->assertHasErrors(['householdName' => 'required']);
        });

        test('default distribution method is required', function () {
            $this->component->set('defaultDistributionMethod', '')
                ->call('register')
                ->assertHasErrors(['defaultDistributionMethod' => 'required']);
        });

        test('user is not authenticated after registration', function () {
            $this->component->call('register');

            $this->assertGuest();
        });
    });
});
