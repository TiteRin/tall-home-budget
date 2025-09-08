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

    public function toMovements()
    {
        $totalProrata = $this->bills->getTotalForDistributionMethod(DistributionMethod::PRORATA);
        $totalEqual = $this->bills->getTotalForDistributionMethod(DistributionMethod::EQUAL);

        return array_map(function (Member $member) {
            $totalMember = $this->bills->getTotalForMember($member);

            // J’ai mal joué mon coup ici
            // À deux personne + 1 compte joint, c’est "facile"
            // on calcule ce que chacun a déjà payé, et on le déduit de ce qui a été payé par le compte joint
            // mais sans compte joint
            // ou si le compte join paie moins que quelqu’un
            // ou à plus de deux
            // il va falloir tricounteriser

            // Je peux déjà faire une version simple, mais il y a un peu de taf ici

            return new Movement($member, null, new Amount(0));
        }, $this->members);
    }
}
