<?php

namespace App\Tests\Feature;

use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can view bills list page', function() {
    $response = $this->get("/bills");

    $response->assertStatus(200);
    $response->assertSeeText("Les dépenses du foyer");
});