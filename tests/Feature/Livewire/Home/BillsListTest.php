<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home\BillsList;
use Livewire;

test('should display the component', function () {
    Livewire::test(BillsList::class)
        ->assertOk();
});
