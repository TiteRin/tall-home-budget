<?php

namespace Tests\Unit\Presenters;

use App\Enums\DistributionMethod;
use App\Http\Resources\HouseholdSummaryResource;
use App\Models\Bill as BillModel;
use App\Models\Household as HouseholdModel;
use App\Models\Member as MemberModel;
use App\Presenters\BillsOverviewPresenter;
use App\Services\Household\CurrentHouseholdServiceContract;
use App\Services\Household\HouseholdSummaryService;
use Illuminate\Support\Collection;

describe("Golder Master", function () {

    test("presenter should produce expected resources structure", function () {
        // Arrange: Household + Bills en mémoire (pas de DB)
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

        $household = new HouseholdModel();
        $household->setAttribute('id', 3001);
        $household->setAttribute('name', 'GM Household');
        $household->setAttribute('default_distribution_method', DistributionMethod::EQUAL);

        $bills = new Collection([$bill]);

        // Important: précharger la relation pour éviter le lazy-loading dans la Resource
        $household->setRelation('bills', $bills);

        // Dummy HouseholdServiceContract pour construire HouseholdSummaryService
        $dummyHouseholdService = new class implements CurrentHouseholdServiceContract {
            public function getHousehold(int $householdId): ?HouseholdModel
            {
                return null;
            }

            public function getCurrentHousehold(): ?HouseholdModel
            {
                return null;
            }
        };

        $summaryService = new HouseholdSummaryService($dummyHouseholdService);

        $presenter = new BillsOverviewPresenter($summaryService);

        // Act
        $result = $presenter->present($household, $bills);

        // Assert: Snapshot du JSON pour stabiliser la sortie
        $normalize = function ($value) {
            if (is_object($value) && method_exists($value, 'toResponse')) {
                return $value->toResponse(request())->getData(true);
            }
            if ($value instanceof HouseholdSummaryResource) {
                return $value->toArray(request());
            }
            return $value;
        };

        $normalized = [
            'bills' => $normalize($result['bills']),
            'household_summary' => $normalize($result['household_summary']),
        ];

        expect(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))->toMatchSnapshot();
    });
});
