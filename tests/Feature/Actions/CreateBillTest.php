<?php

use App\Actions\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Repositories\BillRepository;

test('CreateBill should create a new bill with the correct value', function () {

    $fakeRepository = new class implements BillRepository {
        public Bill|null $lastBill = null;

        public function create(
            string             $name,
            Amount             $amount,
            DistributionMethod $distributionMethod,
            int                $householdId,
            ?int               $memberId = null
        ): Bill
        {
            $this->lastBill = new Bill([
                'name' => $name,
                'amount' => $amount,
                'distribution_method' => $distributionMethod,
                'household_id' => $householdId,
                'member_id' => $memberId
            ]);

            return $this->lastBill;
        }
    };

    $household = bill_factory()->household();
    $action = new CreateBill($fakeRepository);
    $bill = $action->handle(
        'Internet',
        new Amount(4200),
        DistributionMethod::EQUAL,
        null
    );
    expect($bill)->toBeInstanceOf(Bill::class)
        ->and($bill->name)->toBe('Internet')
        ->and($bill->amount)->toEqual(new Amount(4200))
        ->and($bill->distribution_method)->toBe(DistributionMethod::EQUAL)
        ->and($bill->household_id)->toBe($household->id)
        ->and($bill->member_id)->toBe(null);
});
