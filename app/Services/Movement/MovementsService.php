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

        return $balances;
    }

    public function toMovements(): array
    {
        $totalProrata = $this->bills->getTotalForDistributionMethod(DistributionMethod::PRORATA);
        $totalEqual = $this->bills->getTotalForDistributionMethod(DistributionMethod::EQUAL);
        $ratios = $this->getRatiosFromIncome();

        $debts = array_combine(
            array_map(function (Member $member) {
                return $member->id;
            }, $this->members),
            array_map(function (Member $member) use ($totalProrata, $totalEqual, $ratios) {
                $totalMember = $this->bills->getTotalForMember($member);

                // cf. EXAMPLES.md
                $amountProrataForMember = new Amount($totalProrata->toCents() * $ratios[$member->id]);
                $amountEqualForMember = new Amount($totalEqual->toCents() / count($this->members));
                $amountForMember = $amountProrataForMember->add($amountEqualForMember);

                return $amountForMember->subtract($totalMember);
            }, $this->members)
        );

        // séparer les dettes selon si elles sont positives ou négatives
        // (retirer les dettes nulles)
        $positiveDebts = array_filter($debts, function (Amount $amount) {
            return $amount->toCents() > 0;
        });
        $negativeDebts = array_filter($debts, function (Amount $amount) {
            return $amount->toCents() < 0;
        });

        // tant qu’on a au moins un membre avec une dette négative,
        // on effectue des mouvements pour mettre la dette à zéro
        while (count($negativeDebts) > 0) {
            $currentDebt = array_pop($negativeDebts);
        }


        // Si vide, tous les mouvements vont vers le compte joint

        // sinon,

        // si à la fin il reste des membres avec une dette négative, ils piochent dans le compte joint
        // s’il reste des membres avec des dettes positives, ils donnent au compte joint

        return array_map(
            function (Member $member) use ($debts) {

                $debt = $debts[$member->id];

                if ($debt->toCents() > 0) {
                    return new Movement($member, null, $debt);
                }

                return new Movement(null, $member, $debt);
            },
            $this->members
        );
    }
}
