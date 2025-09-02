<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home\AccountsList;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;

uses(RefreshDatabase::class);

describe("When no household exists", function () {

    test('should throw an exception', function () {
        Livewire::test(AccountsList::class);
    })->throws(Exception::class, 'No household exists');

});
