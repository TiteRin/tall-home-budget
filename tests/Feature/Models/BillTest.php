<?php

use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a bill can be created and associated with a household and a member', function () {
    // Arrange: Créez une facture et toutes ses dépendances en une seule ligne !
    $bill = Bill::factory()->create();

    // Assert: Vérifiez que les relations sont correctes et que les données existent.
    expect($bill)->toBeInstanceOf(Bill::class)
        ->and($bill->id)->toBeInt()
        ->and($bill->household)->toBeInstanceOf(Household::class)
        ->and($bill->member)->toBeInstanceOf(Member::class);

    // On peut aussi directement vérifier la présence en base de données
    $this->assertDatabaseHas('bills', [
        'id' => $bill->id,
        'name' => $bill->name
    ]);
});