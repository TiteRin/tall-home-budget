<?php
return;

use App\Actions\Bills\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill as BillModel;
use App\Models\Household as HouseholdModel;
use App\Models\Member as MemberModel;
use App\Repositories\Contracts\BillRepository;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Support\Collection;
use Mockery as m;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function () {
    m::close();
});

it('golden master: getBillsForHousehold with no current household', function () {
    // Arrange
    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);
    $householdService->shouldNotReceive('getHousehold');

    // Pas utilisé dans ce scénario
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdServiceContract::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $result = $service->getBillsForHousehold();

    // Normalize resources to plain arrays for a stable snapshot
    $normalize = function ($value) {
        if (is_object($value) && method_exists($value, 'toResponse')) {
            return $value->toResponse(request())->getData(true);
        }
        return $value;
    };

    $normalized = [
        'bills' => $normalize($result['bills']),
        'household_summary' => $normalize($result['household_summary']),
    ];

    // Assert
    expect(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});

it('golden master: getBillsForHousehold with explicit non-existing household id', function () {
    // Arrange
    $fakeId = 987654321;
    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getHousehold')->once()->with($fakeId)->andReturn(null);
    $householdService->shouldNotReceive('getCurrentHousehold');

    // Pas utilisé dans ce scénario
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdServiceContract::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $result = $service->getBillsForHousehold($fakeId);

    // Normalize
    $normalize = function ($value) {
        if (is_object($value) && method_exists($value, 'toResponse')) {
            return $value->toResponse(request())->getData(true);
        }
        return $value;
    };

    $normalized = [
        'bills' => $normalize($result['bills']),
        'household_summary' => $normalize($result['household_summary']),
    ];

    // Assert
    expect(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});

it('golden master: getBillsForHousehold with a household having one bill and member (happy path)', function () {
    // Arrange: modèles en mémoire (non persistés)
    $member = new MemberModel();
    $member->setAttribute('id', 1001);
    $member->setAttribute('name', 'Alice');

    $bill = new BillModel();
    $bill->setAttribute('id', 2001);
    $bill->setAttribute('name', 'Internet');
    $bill->setAttribute('amount', 10000);
    $bill->setAttribute('distribution_method', 'equal');
    $bill->setAttribute('member_id', 1001);
    $bill->setRelation('member', $member);

    // Household partiel
    /** @var HouseholdModel|Mockery\MockInterface $household */
    $household = m::mock(HouseholdModel::class)->makePartial();
    $household->setAttribute('id', 3001);
    $household->setAttribute('name', 'GM Household');
    $household->setAttribute('default_distribution_method', DistributionMethod::EQUAL);

    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getHousehold')->once()->with(3001)->andReturn($household);

    $billRepository = m::mock(BillRepository::class);
    $billRepository
        ->shouldReceive('listForHousehold')
        ->once()
        ->with(3001)
        ->andReturn(new Collection([$bill]));

    app()->instance(HouseholdServiceContract::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    $service = app(BillService::class);
    $result = $service->getBillsForHousehold(3001);


    // Normalize vers JSON stable
    $normalize = function ($value) {
        if (is_object($value) && method_exists($value, 'toResponse')) {
            return $value->toResponse(request())->getData(true);
        }
        return $value;
    };

    $normalized = [
        'bills' => $normalize($result['bills']),
        'household_summary' => $normalize($result['household_summary']),
    ];

    // Assert Golden Master
    expect(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});

it('golden master: getBillsCollection with no current household', function () {
    // Arrange
    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);
    $householdService->shouldNotReceive('getHousehold');

    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdServiceContract::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $collection = $service->getBillsCollection();

    // Assert
    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection->isEmpty())->toBeTrue();
});

it('golden master: getBillsCollection with a household having one bill and member', function () {
    // Arrange: modèles en mémoire
    $member = new MemberModel();
    $member->setAttribute('id', 1001);
    $member->setAttribute('name', 'Alice');

    $bill = new BillModel();
    $bill->setAttribute('id', 2001);
    $bill->setAttribute('name', 'Internet');
    $bill->setAttribute('amount', 10000);
    $bill->setAttribute('distribution_method', 'equal');
    $bill->setAttribute('member_id', 1001);
    $bill->setRelation('member', $member);

    /** @var HouseholdModel|Mockery\MockInterface $household */
    $household = m::mock(HouseholdModel::class)->makePartial();
    $household->setAttribute('id', 3002);
    $household->setAttribute('name', 'GM Household 2');

    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getHousehold')->once()->with(3002)->andReturn($household);
    $householdService->shouldNotReceive('getCurrentHousehold');

    $billRepository = m::mock(BillRepository::class);
    $billRepository
        ->shouldReceive('listForHousehold')
        ->once()
        ->with(3002)
        ->andReturn(collect([$bill]));


    app()->instance(HouseholdServiceContract::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $collection = $service->getBillsCollection(3002);

    // Normalize minimal pour un snapshot stable (modèles -> tableau simple)
    $asArray = $collection->map(function (BillModel $b) {
        return [
            'id' => $b->id,
            'name' => $b->name,
            'member' => [
                'id' => optional($b->member)->id,
                'name' => optional($b->member)->name,
            ],
        ];
    })->values()->all();

    // Assert via snapshot
    expect(json_encode($asArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});

it('golden master: createBill returns created bill (EQUAL)', function () {
    // Arrange
    $householdId = 4001;
    $memberId = null;
    $name = 'Groceries';
    $amount = new Amount(12345); // 123,45 €
    $method = DistributionMethod::EQUAL;

    $household = new HouseholdModel();
    $household->setAttribute('id', $householdId);

    // Le Bill retourné par le repository (modèle en mémoire, non persisté)
    $returned = new BillModel();
    $returned->setAttribute('id', 9001);
    $returned->setAttribute('name', $name);
    $returned->setAttribute('amount', 12345); // brut (casté en lecture vers Amount)
    $returned->setAttribute('distribution_method', $method);
    $returned->setAttribute('household_id', $householdId);
    $returned->setAttribute('member_id', $memberId);

    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn($household);

    $billRepository = m::mock(BillRepository::class);
    $billRepository
        ->shouldReceive('create')
        ->once()
        ->withArgs(function ($n, $a, $m, $hid, $mid) use ($name, $amount, $method, $householdId, $memberId) {
            return $n === $name
                && $a instanceof Amount && $a->value() === $amount->value()
                && $m === $method
                && $hid === $householdId
                && $mid === $memberId;
        })
        ->andReturn($returned);

    $action = new CreateBill($billRepository, $householdService);

    // Act
    $bill = $action->handle($name, $amount, $method, $memberId);

    // Normalize pour snapshot stable
    $asArray = [
        'id' => $bill->id,
        'name' => $bill->name,
        'amount' => $bill->amount?->value(),
        'distribution_method' => $bill->distribution_method?->value,
        'household_id' => $bill->household_id,
        'member_id' => $bill->member_id,
    ];

    // Assert Golden Master
    expect(json_encode($asArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
});

it('createBill throws when household is not found', function () {
    // Arrange
    $householdId = 4444;
    $householdService = m::mock(HouseholdServiceContract::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);

    $billRepository = m::mock(BillRepository::class); // non utilisé

    $action = new CreateBill($billRepository, $householdService);

    // Assert + Act
    $fn = fn() => $action->handle(
        'Anything',
        new Amount(5000),
        DistributionMethod::PRORATA,
    );

    expect($fn)->toThrow(Exception::class, 'No current household found');
});
