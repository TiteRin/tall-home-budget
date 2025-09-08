<?php

namespace App\Services\Bill;

use Illuminate\Support\Collection;

class BillsCollection
{
    private Collection $bills;

    public function __construct($bills = [])
    {
        $this->bills = collect($bills);
    }

    public function toArray(): array
    {
        return $this->bills->toArray();
    }
}
