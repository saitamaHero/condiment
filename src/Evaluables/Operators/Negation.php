<?php

namespace Condiment\Evaluables\Operators;

use Condiment\Evaluables;

final class Negation extends LogicalOperator
{
    public function evaluate(): bool
    {
        if ($this->evaluablesCount !== 1) {
            //TODO needs to throw an Exception
            throw new \Exception("Error you modofoca");
        }

        return !current($this->evaluables)->evaluate();
    }
}
