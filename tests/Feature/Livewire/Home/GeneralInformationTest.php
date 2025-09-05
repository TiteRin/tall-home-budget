<?php

namespace Tests\Feature\Livewire\Home;

use App\Enums\DistributionMethod;
use App\Livewire\Home\GeneralInformation;
use Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household();
});

test('should display the component', function () {
    Livewire::test(GeneralInformation::class, ['household' => $this->household])
        ->assertStatus(200);
});

test('should display the household distribution method', function () {
    $this->household->default_distribution_method = DistributionMethod::PRORATA;
    Livewire::test(GeneralInformation::class, ['household' => $this->household])
        ->assertSee('Mode de répartition par défaut')
        ->assertSee('Prorata');
});

test('should display if the household has a joint account', function () {
    $this->household->has_joint_account = true;
    Livewire::test(GeneralInformation::class, ['household' => $this->household])
        ->assertSee('Compte joint')
        ->assertSee('Oui');
});
