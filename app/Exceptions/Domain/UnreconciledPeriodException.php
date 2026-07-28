<?php

namespace App\Exceptions\Domain;

class UnreconciledPeriodException extends DomainException
{
    public function __construct(string $message = 'Period has unreconciled import batches and cannot enter review.')
    {
        parent::__construct($message);
    }
}
