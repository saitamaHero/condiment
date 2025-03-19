<?php

namespace Condiment\Exceptions;

class EvaluatorInvalidConditionException extends \Exception
{

    public function __construct(string $condition)
    {
        parent::__construct("The condition '$condition' is not supported by the Evaluator.");
    }
}
