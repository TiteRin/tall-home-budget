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
