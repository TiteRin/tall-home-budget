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

        $balances = collect();

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

        $totalNotAssociated = $this->bills->getTotalForMember();
        if ($totalNotAssociated->toCents() !== 0) {
            $balances->push(new Balance(
                    new Member(['first_name' => 'Compte joint']),
                    $totalNotAssociated
                )
            );
        }


        return $balances;
    }

    public function getCreditors(): Collection
    {
        return $this->computeBalances()->filter(function (Balance $balance) {
            return $balance->isCreditor();
        });
    }

    public function getDebitors(): Collection
    {
        return $this->computeBalances()->filter(function (Balance $balance) {
            return $balance->isDebitor();
        });
    }

    public function toMovements(): Collection
    {
        $movements = collect();

        $creditors = $this->getCreditors();
        $debitors = $this->getDebitors();

        while (!empty($creditors) && !empty($debitors)) {
            $creditor = $creditors->first();
            $debitor = $debitors->first();

            $amount = min(
                $creditor->abs()->toCents(),
                $debitor->abs()->toCents(),
            );

            $movements->push(
                new Movement(
                    memberFrom: $debitor->member,
                    memberTo: $creditor->member,
                    amount: new Amount($amount)
                )
            );

            $creditor->balance->subtract($amount);
            $debitor->balance->add($amount);

            if ($creditor->balance->toCents() === 0) {
                $creditors->shift();
            }

            if ($debitor->balance->toCents() === 0) {
                $debitors->shift();
            }
        }

        return $movements;
    }
}
