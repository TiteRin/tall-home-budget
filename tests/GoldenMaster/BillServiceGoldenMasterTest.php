<?php

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill as BillModel;
use App\Models\Household as HouseholdModel;
use App\Models\Member as MemberModel;
use App\Repositories\BillRepository;
use App\Services\Bill\BillService;
use App\Services\Household\HouseholdService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Mockery as m;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function () {
    m::close();
});

it('golden master: getBillsForHousehold with no current household', function () {
    // Arrange
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);
    $householdService->shouldNotReceive('getHousehold');

    // Pas utilisé dans ce scénario
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
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
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getHousehold')->once()->with($fakeId)->andReturn(null);
    $householdService->shouldNotReceive('getCurrentHousehold');

    // Pas utilisé dans ce scénario
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
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

    // Collection de bills
    $fakeBills = new Collection([$bill]);

    // Relation HasMany factice, conforme au type de retour
    $query = (new BillModel())->newQuery(); // ne déclenche pas de requête tant qu'on ne fait pas ->get() sur le builder
    $hasManyFake = new class($query, $household, $fakeBills) extends HasMany {
        private Collection $fake;

        public function __construct(EloquentBuilder $query, EloquentModel $parent, Collection $fake)
        {
            $this->fake = $fake;
            parent::__construct($query, $parent, 'household_id', $parent->getKeyName());
        }

        public function with($relations)
        {
            return $this; // on ignore les relations, car déjà eager-loadées
        }

        public function get($columns = ['*'])
        {
            return $this->fake;
        }
    };

    // Le Resource de résumé peut rappeler bills() => autorisons plusieurs appels
    $household->shouldReceive('bills')->atLeast()->once()->andReturn($hasManyFake);
    // Précharge aussi la relation en propriété si une Resource y accède directement
    $household->setRelation('bills', $fakeBills);

    $householdId = 3001;

    // Mock HouseholdService
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getHousehold')->once()->with($householdId)->andReturn($household);
    $householdService->shouldNotReceive('getCurrentHousehold');

    // Repository non utilisé
    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
    app()->instance(BillRepository::class, $billRepository);

    /** @var BillService $service */
    $service = app(BillService::class);

    // Act
    $result = $service->getBillsForHousehold($householdId);

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
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getCurrentHousehold')->once()->andReturn(null);
    $householdService->shouldNotReceive('getHousehold');

    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
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

    $fakeBills = collect([$bill]);

    // HasMany factice conforme au type de retour
    $hasManyFake = makeHasManyFake($household, $fakeBills);

    // Autoriser plusieurs appels à bills()
    $household->shouldReceive('bills')->atLeast()->once()->andReturn($hasManyFake);
    $household->setRelation('bills', $fakeBills);

    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getHousehold')->once()->with(3002)->andReturn($household);
    $householdService->shouldNotReceive('getCurrentHousehold');

    $billRepository = m::mock(BillRepository::class);

    app()->instance(HouseholdService::class, $householdService);
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

/**
 * Fabrique une relation HasMany factice qui supporte ->with('member')->get()
 */
function makeHasManyFake(EloquentModel $parent, Collection $fake): HasMany
{
    $query = (new BillModel())->newQuery();

    return new class($query, $parent, $fake) extends HasMany {
        private Collection $fake;

        public function __construct(EloquentBuilder $query, EloquentModel $parent, Collection $fake)
        {
            $this->fake = $fake;
            parent::__construct($query, $parent, 'household_id', $parent->getKeyName());
        }

        public function with($relations)
        {
            return $this; // relations déjà préchargées
        }

        public function get($columns = ['*'])
        {
            return $this->fake;
        }
    };
}

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

    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getHousehold')->once()->with($householdId)->andReturn($household);
    $householdService->shouldNotReceive('getCurrentHousehold');

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

    $service = new BillService($householdService, $billRepository);

    // Act
    $bill = $service->createBill($name, $amount, $method, $householdId, $memberId);

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
    $householdService = m::mock(HouseholdService::class);
    $householdService->shouldReceive('getHousehold')->once()->with($householdId)->andReturn(null);
    $householdService->shouldNotReceive('getCurrentHousehold');

    $billRepository = m::mock(BillRepository::class); // non utilisé

    $service = new BillService($householdService, $billRepository);

    // Assert + Act
    $fn = fn() => $service->createBill(
        'Anything',
        new Amount(5000),
        DistributionMethod::PRORATA,
        $householdId
    );

    expect($fn)->toThrow(\InvalidArgumentException::class, 'Household not found');
});
