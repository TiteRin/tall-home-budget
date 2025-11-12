<?php

test('can access Login Page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
