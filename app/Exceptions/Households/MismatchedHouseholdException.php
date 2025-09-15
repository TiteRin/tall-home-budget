<?php

namespace App\Exceptions\Households;

    use Exception;

    class MismatchedHouseholdException extends Exception
    {
        public function __construct($message = "The member is not associated with the household")
        {
            parent::__construct($message);
        }
    }
