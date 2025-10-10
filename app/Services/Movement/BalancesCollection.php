<?php

namespace App\Services\Movement;

use App\Domains\ValueObjects\Balance;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, Balance>
 */
class BalancesCollection extends Collection
{

    public function getCreditors(): BalancesCollection
    {
        return $this->filter(function (Balance $balance) {
            return $balance->isCreditor();
        });
    }

    public function getDebitors(): BalancesCollection
    {
        return $this->filter(function (Balance $balance) {
            return $balance->isDebitor();
        });
    }

    public function filter(callable $callback = null): BalancesCollection
    {
        return new static(parent::filter($callback));
    }

    public function map(callable $callback): Collection
    {
        return parent::map($callback);;
    }
}

