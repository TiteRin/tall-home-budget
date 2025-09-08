<?php

namespace App\Services\Movement;

use App\Enums\DistributionMethod;
use App\Services\Bill\BillsCollection;

class MovementsService
{
    private BillsCollection $bills;
    private array $incomes;

    public function __construct(
        BillsCollection $bills,
        array           $incomes,
    )
    {
        $this->bills = $bills;
        $this->incomes = $incomes;
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
        return [];
    }
}
