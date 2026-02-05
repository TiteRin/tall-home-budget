<?php

namespace App\Services\Movement;

use App\Domains\ValueObjects\Balance;
use App\Support\Collections\TypedCollection;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, Balance>
 */
class BalancesCollection extends TypedCollection
{

    protected function getExpectedType(): string
    {
        return Balance::class;
    }

    protected function getCollectionName(): string
    {
        return self::class;
    }

    public function getCreditors(): BalancesCollection
    {
        return $this->filter(function (Balance $balance) {
            return $balance->isCreditor();
        });
    }

    public function getDebtors(): BalancesCollection
    {
        return $this->filter(function (Balance $balance) {
            return $balance->isDebitor();
        });
    }
}

