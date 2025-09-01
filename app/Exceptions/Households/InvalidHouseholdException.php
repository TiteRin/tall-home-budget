<?php

namespace App\Exceptions\Households;

use Exception;

class InvalidHouseholdException extends Exception
{
    public function __construct($message = "The household is not configured to have joint account")
    {
        parent::__construct($message);
    }
}
