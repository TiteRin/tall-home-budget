<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillsManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household();
});

test('should display an empty table if no bills', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Aucune dépense');
});

test('should use the BillForm component to add a bill', function () {
    Livewire::test(BillsManager::class)
        ->assertSeeLivewire('bill-form');
});

describe('when there’s a list of 5 bills', function () {
    beforeEach(function () {
        $member = bill_factory()->member([], $this->household);
        $this->bills = bill_factory()->bills(5, [], $member, $this->household);
    });

    test('the component bill-row should have been called', function () {
        Livewire::test(BillsManager::class)
            ->assertSeeLivewire('bills.row');
    });

    test('should display 5 bills', function () {
        $component = Livewire::test(BillsManager::class);
        $component->assertSeeHtmlInOrder(
            $this->bills->map(fn($bill) => $bill->name)->toArray()
        );
    });

    test('should remove a bill when a billDeleted is triggered', function () {
        $bill = bill_factory()->bill(['name' => 'Électricité']);

        Livewire::test(BillsManager::class)
            ->dispatch('billDeleted', billId: $bill->id)
            ->assertDontSee('Aucune dépense')
            ->assertDontSee('Électricité');
    });
});

test('should refresh bills collection when refreshBills is triggered', function () {

    $member = bill_factory()->member([], $this->household);
    $bills = bill_factory()->bills(5, [], $member, $this->household);

    $component = Livewire::test(BillsManager::class);

    $newBill = bill_factory()->bill(['name' => 'Nouvelle dépense'], $member, $this->household);

    $component
        ->dispatch('refreshBills')
        ->assertSee('Nouvelle dépense');
});

test('should handle complete workflow: add bill, then delete it', function () {
    $member = bill_factory()->member([], $this->household);

    $component = Livewire::test(BillsManager::class);

    // État initial - pas de dépenses
    $component->assertSee('Aucune dépense');

    // Ajouter une dépense via un événement (simulant BillForm)
    $bill = bill_factory()->bill(['name' => 'Facture test'], $member, $this->household);
    $component->dispatch('refreshBills');

    // Vérifier que la dépense est affichée
    $component->assertSee('Facture test');
    $component->assertDontSee('Aucune dépense');

    // Supprimer la dépense
    $component->dispatch('billDeleted', billId: $bill->id);

    // Vérifier que la dépense n'est plus affichée
    $component->assertDontSee('Facture test');
    $component->assertSee('Aucune dépense');
});


test('should return correct household members', function () {
    $member1 = bill_factory()->member(['first_name' => 'John'], $this->household);
    $member2 = bill_factory()->member(['first_name' => 'Jane'], $this->household);

    $component = Livewire::test(BillsManager::class);

    expect($component->get('householdMembers'))
        ->toHaveCount(2)
        ->and($component->get('householdMembers')->pluck('first_name')->toArray())
        ->toContain('John', 'Jane');
});

test('should return correct default distribution method', function () {
    // Configurer le foyer avec une méthode par défaut
    $this->household->update(['default_distribution_method' => DistributionMethod::PRORATA]);

    $component = Livewire::test(BillsManager::class);

    expect($component->get('defaultDistributionMethod'))
        ->toBe(DistributionMethod::PRORATA);
});

test('should handle large number of bills efficiently', function () {
    $member = bill_factory()->member([], $this->household);
    $bills = bill_factory()->bills(50, [], $member, $this->household);

    $startTime = microtime(true);

    $component = Livewire::test(BillsManager::class);

    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;

    expect($executionTime)->toBeLessThan(1.0)
        ->and($component->get('bills'))->toHaveCount(50);

});

test('should handle bill deletion when bill does not exist', function () {
    $component = Livewire::test(BillsManager::class);

    // Essayer de supprimer une dépense inexistante
    expect(fn() => $component->dispatch('billDeleted', billId: 99999))
        ->toThrow(ModelNotFoundException::class);
});


test('should maintain correct bills after deletion', function () {
    $member = bill_factory()->member([], $this->household);
    $bills = collect([
        bill_factory()->bill(['name' => 'Première dépense'], $member, $this->household),
        bill_factory()->bill(['name' => 'Deuxième dépense'], $member, $this->household),
        bill_factory()->bill(['name' => 'Troisième dépense'], $member, $this->household),
    ]);

    $component = Livewire::test(BillsManager::class);

    // Vérifier l'état initial
    expect($component->get('bills'))->toHaveCount(3);

    $initialBillNames = $component->get('bills')->pluck('name')->toArray();
    expect($initialBillNames)->toContain('Première dépense', 'Deuxième dépense', 'Troisième dépense');

    // Supprimer la deuxième dépense
    $component->dispatch('billDeleted', billId: $bills[1]->id);

    // Vérifier l'état après suppression
    expect($component->get('bills'))->toHaveCount(2);

    $remainingBillNames = $component->get('bills')->pluck('name')->toArray();
    expect($remainingBillNames)
        ->toContain('Première dépense', 'Troisième dépense')
        ->and($remainingBillNames)
        ->not->toContain('Deuxième dépense');

    // Vérifier que l'ordre est maintenu (première avant troisième)
    $firstIndex = array_search('Première dépense', $remainingBillNames);
    $thirdIndex = array_search('Troisième dépense', $remainingBillNames);
    expect($firstIndex)->toBeLessThan($thirdIndex);
});

describe('Édition de dépense', function () {
    beforeEach(function () {
        $this->member = bill_factory()->member([], $this->household);
        $this->bill = bill_factory()->bill(['name' => 'Facture test'], $this->member, $this->household);
    });

    test('should switch to edit mode when editBill is triggered', function () {
        $component = Livewire::test(BillsManager::class);

        expect($component->get('isEditing'))->toBeFalse()
            ->and($component->get('editingBillId'))->toBeNull();

        $component->dispatch('editBill', billId: $this->bill->id);

        expect($component->get('isEditing'))->toBeTrue()
            ->and($component->get('editingBillId'))->toBe($this->bill->id);
    });

    test('should quit edit mode when cancelEdit is triggered', function () {
        $component = Livewire::test(BillsManager::class);
        $component
            ->set('isEditing', true)
            ->set('editingBillId', 10)
            ->dispatch('cancelEditBill');

        expect($component->get('isEditing'))->toBeFalse()
            ->and($component->get('editingBillId'))->toBeNull();
    });

    test('should quit edit mode et refresh bills when billHasBeenUpdated is triggered', function () {
        $component = Livewire::test(BillsManager::class);

        $component
            ->set('isEditing', true)
            ->set('editingBillId', 10)
            ->dispatch('billHasBeenUpdated');

        expect($component->get('isEditing'))->toBeFalse()
            ->and($component->get('editingBillId'))->toBeNull();
    });

    test('shouldn’t edit a non existing bill', function () {
        $component = Livewire::test(BillsManager::class);
        $component->dispatch('editBill', billId: 99999);
    })->throws(ModelNotFoundException::class);

    test('when editing, should use a BillForm component', function () {
        $component = Livewire::test(BillsManager::class);
        $component->dispatch('editBill', billId: $this->bill->id);
        $component->assertSeeLivewire('bill-form');
    });
});

