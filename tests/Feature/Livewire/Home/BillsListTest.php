<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home\BillsList;
use Livewire;

test('should display the component', function () {
    Livewire::test(BillsList::class)
        ->assertOk();
});

describe("no bills are passed", function () {
    test('should display a message', function () {
        Livewire::test(BillsList::class)
            ->assertSee("Aucune dépense");
    });

    test('should display a link to the bills manager', function () {
        Livewire::test(BillsList::class)
            ->assertSee("Paramétrer les dépenses")
            ->assertSeeHtml('href="' . route('bills.settings') . '"');
    });
});
