<?php

use App\Enums\DistributionMethod;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

test('can access Login Page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('can see email and password fields', function () {
    Livewire::test(Login::class)
        ->assertSeeHtml('name="email"')
        ->assertSeeHtml('type="password"');
});

test('a user created via registration can login', function () {
    // 1. Register a user
    Livewire::test(Register::class)
        ->set('firstName', 'Test')
        ->set('lastName', 'User')
        ->set('email', 'test-register@example.com')
        ->set('password', 'password123!')
        ->set('passwordConfirmation', 'password123!')
        ->set('householdName', 'Test Household')
        ->set('defaultDistributionMethod', DistributionMethod::EQUAL->value)
        ->set('hasJointAccount', false)
        ->call('register')
        ->assertRedirect(route('login'));

    $this->assertTrue(User::whereEmail('test-register@example.com')->exists());

    // 2. Login with the newly registered user
    Livewire::test(Login::class)
        ->set('email', 'test-register@example.com')
        ->set('password', 'password123!')
        ->call('authenticate')
        ->assertRedirect(route('home'));

    $user = User::whereEmail('test-register@example.com')->first();
    $this->assertAuthenticatedAs($user);
});

test('can fill fields and submit form', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors();
});

test('when using wrong credentials, should display an error message', function () {
    Livewire::test(Login::class)
        ->set('email', 'wrong@example.com')
        ->set('password', 'wrongpassword')
        ->call('authenticate')
        ->assertHasErrors(['email']);
});

test('when using correct credentials, should redirect to home page', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'i-love-laravel'),
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', $password)
        ->call('authenticate')
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});
