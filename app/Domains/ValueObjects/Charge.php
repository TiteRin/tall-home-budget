<?php

namespace App\Domains\ValueObjects;

use App\Enums\DistributionMethod;
use App\Models\Member;

class Charge
{
    protected ?Amount $amount = null;
    protected ?DistributionMethod $distribution_method = null;
    protected ?Member $payer = null;

    private function __construct(?Amount $amount = null, ?DistributionMethod $distribution_method = null, ?Member $payer = null)
    {
        $this->amount = $amount;
        $this->distribution_method = $distribution_method;
        $this->payer = $payer;
    }

    public function withAmount(Amount $amount): Charge
    {
        return new self(
            $amount,
            $this->distribution_method,
            $this->payer
        );
    }

    public function withDistributionMethod(DistributionMethod $distribution_method): Charge
    {
        return new self(
            $this->amount,
            $distribution_method,
            $this->payer
        );
    }

    public function withPayer(?Member $payer): Charge
    {
        return new self(
            $this->amount,
            $this->distribution_method,
            $payer
        );
    }

    public function getAmountOrZero(): ?Amount
    {
        return $this->amount ?? Amount::zero();
    }

    public function getDistributionMethod(): ?DistributionMethod
    {
        return $this->distribution_method;
    }

    public function getPayer(): ?Member
    {
        return $this->payer;
    }

    public static function create(): Charge
    {
        return new self();
    }
}
