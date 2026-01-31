<?php

namespace Tests\Feature\Http;

describe('Mentions légales', function () {

    test('should display "Mentions légales"', function () {
        $this->get('/mentions-legales')
            ->assertSeeText("Mentions légales");
    });
});

describe('CGU', function () {
    test('should display "Conditions Générales d’Utilisation"', function () {
        $this->get('/cgu')
            ->assertSeeText("Conditions Générales d’Utilisation");
    });
});

describe('Confidentialités', function () {

    test('should display "Politique de confidentialité"', function () {
        $this->get('/confidentialite')
            ->assertSeeText("Politique de confidentialité");
    });
});



describe('Footer', function () {
    test('should display legal links on home page', function () {
        $this->get("/")
            ->assertSeeText("Mentions légales")
            ->assertSeeText("CGU");
    });
});
