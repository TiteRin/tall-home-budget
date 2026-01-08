<?php

namespace Tests\Feature;

test('welcome page displays app content and navigation buttons', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Home Budget');
    $response->assertSee('Gérez votre budget domestique en toute simplicité');
    $response->assertSee('Créer un compte');
    $response->assertSee('Se connecter');
    $response->assertSee(route('register'));
    $response->assertSee(route('login'));
});
