<?php

namespace App\Services\Movement;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Balance;
use App\Enums\DistributionMethod;
use App\Models\Member;
use App\Services\Bill\BillsCollection;
use Illuminate\Support\Collection;

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

    public function computeBalances(): Collection
    {
        $totalEqual = $this->bills->getTotalForDistributionMethod(DistributionMethod::EQUAL);
        $totalProrata = $this->bills->getTotalForDistributionMethod(DistributionMethod::PRORATA);
        $ratios = $this->getRatiosFromIncome();

        $balances = new BalancesCollection();

        foreach ($this->members as $member) {
            $paid = $this->bills->getTotalForMember($member);
            $owedEqual = new Amount($totalEqual->toCents() / count($this->members));
            $owedProrata = new Amount($totalProrata->toCents() * $ratios[$member->id]);
            $owed = $owedEqual->add($owedProrata);

            $balances->push(
                new Balance(
                    $member,
                    $paid->subtract($owed)
                )
            );
        }

        $joint = $this->members[0]->household->jointAccount();
        if ($joint) {
            $balances->push(
                new Balance(
                    $joint,
                    $this->bills->getTotalForMember()
                )
            );
        }

        return $balances;
    }

    public function toMovements(): Collection
    {
        $movements = collect();

        $balances = $this->computeBalances();
        $creditors = $balances->getCreditors();
        $debitors = $balances->getDebitors();

        $creditor = $creditors->shift();
        $debitor = $debitors->shift();

        while ($creditor !== null && $debitor !== null) {

            $amount = new Amount(min(
                $creditor->abs()->toCents(),
                $debitor->abs()->toCents(),
            ));

            $movements->push(
                new Movement(
                    memberFrom: $debitor->member,
                    memberTo: $creditor->member,
                    amount: $amount
                )
            );

            $creditor->amount = $creditor->amount->subtract($amount);
            $debitor->amount = $debitor->amount->add($amount);

            if ($creditor->amount->toCents() === 0) {
                $creditor = $creditors->shift();
            }

            if ($debitor->amount->toCents() === 0) {
                $debitor = $debitors->shift();
            }
        }

        return $movements;
    }
}
