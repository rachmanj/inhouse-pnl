<?php

namespace App\Exceptions\Domain;

use Exception;

class PeriodLockedException extends Exception
{
    public function __construct(string $message = 'This report period is locked and cannot be modified.')
    {
        parent::__construct($message);
    }
}
