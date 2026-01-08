<?php

use App\Livewire\Auth\Login;
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
