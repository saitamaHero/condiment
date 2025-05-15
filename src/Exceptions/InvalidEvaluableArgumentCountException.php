<?php

namespace Condiment\Exceptions;

class InvalidEvaluableArgumentCountException extends \Exception
{
    public function __construct($expectedCount, $actualCount, $code = 0, ?\Throwable $previous = null)
    {
        $message = "Invalid number of evaluable arguments provided. Expected {$expectedCount}, got {$actualCount}.";

        parent::__construct($message, $code, $previous);
    }
}
