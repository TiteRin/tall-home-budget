<?php

namespace App\Services\Movement;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Member;
use App\Services\Bill\BillsCollection;

class MovementsService
{
    private BillsCollection $bills;
    private array $incomes;
    private array $members;

    public function __construct(
        array $members,
        BillsCollection $bills,
        array           $incomes,
    )
    {
        $this->bills = $bills;
        $this->incomes = $incomes;
        $this->members = $members;
    }

    public function getTotalsAmount(): array
    {
        $totals = array_map(function (DistributionMethod $method) {
            return [$method->value => $this->bills->getTotalForDistributionMethod($method)];
        }, DistributionMethod::cases());

        return array_merge(['total' => $this->bills->getTotal()], ...$totals);
    }

    public function getTotalIncome(): Amount
    {
        return array_reduce(
            $this->incomes,
            function (Amount $carry, Amount $income) {
                return $carry->add($income);
            },
            new Amount(0)
        );
    }

    public function getRatiosFromIncome(): array
    {
        $totalIncome = $this->getTotalIncome();
        return array_combine(
            array_map(
                function (Member $member) {
                    return $member->id;
                },
                $this->members
            ),
            array_map(
                function (Member $member) use ($totalIncome) {
                    return $this->incomes[$member->id]->toCents() / $totalIncome->toCents();
                },
                $this->members
            )
        );
    }

    public function toMovements()
    {
        $totalProrata = $this->bills->getTotalForDistributionMethod(DistributionMethod::PRORATA);
        $totalEqual = $this->bills->getTotalForDistributionMethod(DistributionMethod::EQUAL);
        $ratios = $this->getRatiosFromIncome();

        return array_map(function (Member $member) use ($totalProrata, $totalEqual, $ratios) {
            $totalMember = $this->bills->getTotalForMember($member);

            // cf. EXAMPLES.md
            $amountProrataForMember = new Amount($totalProrata->toCents() * $ratios[$member->id]);
            $amountEqualForMember = new Amount($totalEqual->toCents() / count($this->members));
            $amountForMember = $amountProrataForMember->add($amountEqualForMember);

            $debt = $totalMember->subtract($amountForMember);

            if ($debt->toCents() > 0) {
                return new Movement($member, null, $debt);
            }

            return new Movement(null, $member, $debt);
        }, $this->members);
    }
}
