<?php

namespace Tests\Support;

use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Support\Collection;

class BillFactory
{
    public function household(array $overrides = []): Household
    {
        $default = [
            'name' => 'Test Household',
            'default_distribution_method' => DistributionMethod::EQUAL,
        ];

        return Household::factory()->create(array_merge($default, $overrides));
    }

    public function householdWithMembers(
        int   $count = 2,
        array $household = [],
        array $member = []
    ): array
    {
        $hh = $this->household($household);
        $members = $this->members($count, $member, $hh);
        return [$hh, $members];
    }

    public function member(array $overrides = [], ?Household $household = null): Member
    {
        $default = [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $attrs = array_merge($default, $overrides);

        if ($household) {
            $attrs['household_id'] = $household->id;
        }

        return Member::factory()->create($attrs);
    }

    public function members(
        int        $count = 2,
        array      $overrides = [],
        ?Household $household = null
    ): Collection
    {
        $hh = $household ?? $this->household();
        return Member::factory()
            ->count($count)
            ->create(
                array_merge(['household_id' => $hh->id],
                    $overrides
                )
            );
    }


    public function bill(array $overrides = [], ?Member $member = null, ?Household $household = null): Bill
    {
        $default = [
            'name' => 'Test Bill',
            'amount' => 10000,
            'distribution_method' => DistributionMethod::EQUAL,
        ];

        $attrs = array_merge($default, $overrides);

        if ($member) {
            $attrs['member_id'] = $member->id;
            $attrs['household_id'] = $member->household_id;
        } elseif ($household) {
            $attrs['household_id'] = $household->id;
        }

        if (!array_key_exists('household_id', $attrs)) {
            $household = $household ?? $this->household();
            $attrs['household_id'] = $household->id;
        }

        if (isset($attrs['household_id']) && !array_key_exists('member_id', $attrs)) {
            $attrs['member_id'] = Member::factory()->create(['household_id' => $attrs['household_id']])->id;;
        }

        return Bill::factory()->create($attrs);
    }

    public function bills(
        int        $count = 2,
        array      $overrides = [],
        ?Member    $member = null,
        ?Household $household = null
    ): Collection
    {
        $bills = collect();
        for ($i = 0; $i < $count; $i++) {
            $bills->push($this->bill($overrides, $member, $household));
        }
        return $bills;
    }
}
