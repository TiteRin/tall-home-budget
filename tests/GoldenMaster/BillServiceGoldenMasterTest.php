<?php

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
    expect($collection)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($collection->isEmpty())->toBeTrue();
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
function makeHasManyFake(\Illuminate\Database\Eloquent\Model $parent, \Illuminate\Support\Collection $fake): HasMany
{
    $query = (new BillModel())->newQuery();

    return new class($query, $parent, $fake) extends HasMany {
        private \Illuminate\Support\Collection $fake;

        public function __construct(EloquentBuilder $query, EloquentModel $parent, \Illuminate\Support\Collection $fake)
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
