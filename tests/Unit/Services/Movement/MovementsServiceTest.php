<?php

namespace Tests\Unit\Services\Movement;

use App\Domains\Entities\JointAccount;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\User;
use App\Services\Bill\BillsCollection;
use App\Services\Movement\MovementsService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use InvalidArgumentException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->household = bill_factory()->household(['has_joint_account' => true]);
    $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
    $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);
    $this->members = [$this->memberAlice, $this->memberBob];
    $this->bills = new BillsCollection([
        bill_factory()->bill(['name' => 'Bill 1', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberAlice, $this->household),
        bill_factory()->bill(['name' => 'Bill 2', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA], $this->memberBob, $this->household),
        bill_factory()->bill(['name' => 'Bill 3', 'amount' => 10000, 'distribution_method' => DistributionMethod::PRORATA, 'member_id' => null], null, $this->household),
    ]);
    $this->incomes = [
        $this->memberAlice->id => new Amount(200000),
        $this->memberBob->id => new Amount(200000),
    ];

    $user = User::factory()->create(['member_id' => $this->memberAlice->id]);
    $this->actingAs($user);
});

test('should return a collection of movements from bills and incomes', function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        $this->incomes,
    );
    expect($service->toMovements())->toBeInstanceOf(Collection::class);
});

describe("Computes balances", function () {

    beforeEach(function () {
        $this->service = new MovementsService(
            $this->members,
            $this->bills,
            []
        );
    });

    test("should throw an exception if no income have been set", function () {
        $this->service->computeBalances();
    })->throws(Exception::class, "You need to set income for every member.");

    test("should throw an exception if not every member has an income", function () {
        $this->service
            ->setIncomes([
                $this->memberAlice->id => new Amount(200000),
            ])->computeBalances();
    })->throws(Exception::class, "You need to set income for every member.");
});

describe("Example.md test", function () {

    describe("Example 1", function () {


        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::EQUAL,
                'member_id' => null
            ], null, $household);

            $electricity = bill_factory()->bill([
                'name' => 'Électricité',
                'amount' => 9000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberAlice, $household);

            $internet = bill_factory()->bill([
                'name' => 'Internet',
                'amount' => 3000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberBob, $household);

            $veterinaire = bill_factory()->bill([
                'name' => 'Vétérinaire',
                'amount' => 10000,
                'distribution_method' => DistributionMethod::EQUAL,
            ], $memberBob, $household);

            $members = [$memberAlice, $memberBob];
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $incomes = [
                $memberAlice->id => new Amount(200000),
                $memberBob->id => new Amount(100000),
            ];

            $this->movementService = new MovementsService($members, $bills, $incomes);
            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
        });

        test("computeBalance()", function () {
            $balances = $this->movementService->computeBalances();

            expect($balances)->toHaveCount(3)
                ->and($balances[0]->member)->toBe($this->memberAlice)
                ->and($balances[0]->amount)->toEqual(new Amount(-39000))
                ->and($balances[1]->member)->toBe($this->memberBob)
                ->and($balances[1]->amount)->toEqual(new Amount(-31000))
                ->and($balances[2]->amount)->toEqual(new Amount((70000)));
        });

        test("getCreditors", function () {
            $creditors = $this->movementService->computeBalances()->getCreditors();
            expect($creditors)->toHaveCount(1);
        });

        test("getDebitors", function () {
            $debitors = $this->movementService->computeBalances()->getDebitors();
            expect($debitors)->toHaveCount(2);
        });

        test("toMovements()", function () {

            $movements = $this->movementService->toMovements();
            expect($movements)->toBeInstanceOf(Collection::class)
                ->and($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBeInstanceOf(JointAccount::class)
                ->and($movements[0]->amount)->toEqual(new Amount(39000))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBeInstanceOf(JointAccount::class)
                ->and($movements[1]->amount)->toEqual(new Amount(31000));
        });
    });

    describe("Example 2", function () {

        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => false]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);
            $memberCharlie = bill_factory()->member(['first_name' => 'Charlie'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberCharlie, $household);

            $electricity = bill_factory()->bill([
                'name' => 'Électricité',
                'amount' => 9000,
                'distribution_method' => DistributionMethod::EQUAL,
            ], $memberAlice, $household);

            $internet = bill_factory()->bill([
                'name' => 'Internet',
                'amount' => 3000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberBob, $household);

            $veterinaire = bill_factory()->bill([
                'name' => 'Vétérinaire',
                'amount' => 10000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberAlice, $household);

            $members = [$memberAlice, $memberBob, $memberCharlie];
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $incomes = [
                $memberAlice->id => new Amount(200000),
                $memberBob->id => new Amount(100000),
                $memberCharlie->id => new Amount(200000),
            ];

            $this->movementService = new MovementsService($members, $bills, $incomes);
            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
            $this->memberCharlie = $memberCharlie;
        });

        test("computeBalances()", function () {
            $balances = $this->movementService->computeBalances();

            expect($balances)->toHaveCount(3)
                ->and($balances[0]->member)->toBe($this->memberAlice)
                ->and($balances[0]->amount)->toEqual(new Amount(-17200))
                ->and($balances[1]->member)->toBe($this->memberBob)
                ->and($balances[1]->amount)->toEqual(new Amount(-16600))
                ->and($balances[2]->member)->toBe($this->memberCharlie)
                ->and($balances[2]->amount)->toEqual(new Amount(33800));
        });

        test('toMovements()', function () {

            $movements = $this->movementService->toMovements();
            expect($movements)->toBeInstanceOf(Collection::class)
                ->and($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[0]->amount)->toEqual(new Amount(17200))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[1]->amount)->toEqual(new Amount(16600));
        });
    });

    describe("Example 3 - avec compte joint, mais négatif", function () {

        beforeEach(function () {
            $household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);

            $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $household);
            $memberBob = bill_factory()->member(['first_name' => 'Bob'], $household);
            $memberCharlie = bill_factory()->member(['first_name' => 'Charlie'], $household);

            $loyer = bill_factory()->bill([
                'name' => 'Loyer',
                'amount' => 70000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberCharlie, $household);

            $electricity = bill_factory()->bill([
                'name' => 'Électricité',
                'amount' => 9000,
                'distribution_method' => DistributionMethod::EQUAL,
            ], $memberAlice, $household);

            $internet = bill_factory()->bill([
                'name' => 'Internet',
                'amount' => 3000,
                'distribution_method' => DistributionMethod::PRORATA,
            ], $memberBob, $household);

            $veterinaire = bill_factory()->bill([
                'name' => 'Vétérinaire',
                'amount' => 10000,
                'distribution_method' => DistributionMethod::PRORATA,
                'member_id' => null
            ], null, $household);

            $members = [$memberAlice, $memberBob, $memberCharlie];
            $bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

            $incomes = [
                $memberAlice->id => new Amount(200000),
                $memberBob->id => new Amount(100000),
                $memberCharlie->id => new Amount(200000),
            ];

            $this->movementService = new MovementsService($members, $bills, $incomes);
            $this->memberAlice = $memberAlice;
            $this->memberBob = $memberBob;
            $this->memberCharlie = $memberCharlie;
        });

        test("computeBalances()", function () {
            $balances = $this->movementService->computeBalances();

            expect($balances)->toHaveCount(4)
                ->and($balances[0]->member)->toBe($this->memberAlice)
                ->and($balances[0]->amount)->toEqual(new Amount(-27200))
                ->and($balances[1]->member)->toBe($this->memberBob)
                ->and($balances[1]->amount)->toEqual(new Amount(-16600))
                ->and($balances[2]->member)->toBe($this->memberCharlie)
                ->and($balances[2]->amount)->toEqual(new Amount(33800))
                ->and($balances[3]->member)->toBeInstanceOf(JointAccount::class)
                ->and($balances[3]->amount)->toEqual(new Amount(10000));
        });

        test('toMovements()', function () {

            $movements = $this->movementService->toMovements();
            expect($movements)->toBeInstanceOf(Collection::class)
                ->and($movements)->toHaveCount(3)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[0]->amount)->toEqual(new Amount(27200))
                ->and($movements[1]->memberFrom)->toBe($this->memberBob)
                ->and($movements[1]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[1]->amount)->toEqual(new Amount(6600))
                ->and($movements[2]->memberFrom)->toBe($this->memberBob)
                ->and($movements[2]->memberTo)->toBeInstanceOf(JointAccount::class)
                ->and($movements[2]->amount)->toEqual(new Amount(10000));
        });
    });
});

test('should obtain the bills total amount in an array', function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        $this->incomes,
    );

    $totals = $service->getTotalsAmount();
    expect($totals)->toBeArray()
        ->and($totals)->toHaveCount(3)
        ->and($totals)->toMatchArray(
            [
                'total' => new Amount(30000),
                'prorata' => new Amount(30000),
                'equal' => new Amount(0),
            ]
        );
});

test("should obtain the total of incomes", function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        [
            $this->memberAlice->id => new Amount(200000),
            $this->memberBob->id => new Amount(200000),
        ],
    );

    $totalIncome = $service->getTotalIncome();
    expect($totalIncome)->toBeInstanceOf(Amount::class)
        ->and($totalIncome)->toEqual(new Amount(400000));;
});

test('should obtain the ratios of incomes', function () {
    $service = new MovementsService(
        $this->members,
        $this->bills,
        [
            $this->memberAlice->id => new Amount(200000),
            $this->memberBob->id => new Amount(200000),
        ],
    );

    $ratios = $service->getRatiosFromIncome();

    expect($ratios)->toBeArray()
        ->and($ratios)->toHaveCount(2)
        ->and($ratios)->toMatchArray(
            [
                $this->memberAlice->id => 0.5,
                $this->memberBob->id => 0.5,
            ]
        );
});

describe("Instantiation via a static method", function () {

    test("should initialize with current household members and bills and no incomes", function () {
        $service = MovementsService::create();

        expect($service)->toBeInstanceOf(MovementsService::class)
            ->and($service->toMovements())->toHaveCount(0);
    });

    test("should update incomes and have movements", function () {
        $service = MovementsService::create();
        $service->setIncomes($this->incomes);

        expect($service->toMovements())->toHaveCount(2);
    });

    test("should add or update income for a member", function () {
        $service = MovementsService::create();

        $service->setIncomeFor($this->memberAlice, new Amount(200000));
        $service->setIncomeFor($this->memberBob, new Amount(200000));

        expect($service->toMovements())->toHaveCount(2);
    });

    test("should not be possible to set an income for a non existing member", function () {
        $service = MovementsService::create();

        $service->setIncomeFor(bill_factory()->member(), new Amount(200000));
    })->throws(InvalidArgumentException::class);
});
