<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillsManager;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Livewire\Livewire;

describe('BillsManager Component', function () {
    beforeEach(function () {
        $this->household = Household::factory()->create();
        $this->member = Member::factory()->create(['household_id' => $this->household->id]);
        $this->user = User::factory()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);
    });

    it('can remove a bill', function () {
        $bill = Bill::factory()->create([
            'household_id' => $this->household->id,
            'member_id' => $this->member->id,
            'amount' => 100,
            'distribution_method' => DistributionMethod::EQUAL
        ]);

        Livewire::test(BillsManager::class)
            ->call('removeBill', $bill->id)
            ->assertDispatched('notify');

        expect(Bill::find($bill->id))->toBeNull();
    });

    it('can start and cancel editing a bill', function () {
        $bill = Bill::factory()->create([
            'household_id' => $this->household->id,
            'member_id' => $this->member->id,
            'amount' => 100,
            'distribution_method' => DistributionMethod::EQUAL
        ]);

        Livewire::test(BillsManager::class)
            ->call('editBill', $bill->id)
            ->assertSet('isEditing', true)
            ->assertSet('editingBillId', $bill->id)
            ->call('cancelEditBill')
            ->assertSet('isEditing', false)
            ->assertSet('editingBillId', null);
    });
    it('handles exception when removing a bill', function () {
        $bill = Bill::factory()->create([
            'household_id' => $this->household->id,
            'member_id' => $this->member->id,
            'amount' => 100,
            'distribution_method' => DistributionMethod::EQUAL
        ]);

        // Instead of mocking the readonly service, we just pass an invalid ID
        // that will cause an exception if the code doesn't handle it,
        // or we check how it handles it.
        // Actually removeBill calls Bill::find($billId) and then throws ModelNotFound if not found.

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Livewire::test(BillsManager::class)
            ->call('removeBill', 99999);
    });

    it('can refresh bills', function () {
        Livewire::test(BillsManager::class)
            ->call('refreshBills')
            ->assertDispatched('notify');
    });

    it('can save a bill and refresh', function () {
        Livewire::test(BillsManager::class)
            ->call('saveBill')
            ->assertSet('isEditing', false)
            ->assertSet('editingBillId', null);
    });
});
