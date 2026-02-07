<?php

namespace Tests\Feature;

test('welcome page displays app content and navigation buttons', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee(config('app.name'));
    $response->assertSee('En couple ? En colocation ? Gérez votre budget domestique en tout simplicité !');
    $response->assertSee('Créer un compte');
    $response->assertSee('Se connecter');
    $response->assertSee(route('register'));
    $response->assertSee(route('login'));
});
