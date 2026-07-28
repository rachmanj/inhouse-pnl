<?php

namespace App\Exceptions\Domain;

class InvalidPeriodTransitionException extends DomainException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition period from {$from} to {$to}.");
    }
}
