<?php

namespace Condiment\Evaluables\Operators;

use Condiment\Exceptions\InvalidEvaluableArgumentCountException;

final class Negation extends LogicalOperator
{
    public function evaluate(): bool
    {
        if ($this->evaluablesCount !== 1) {
            throw new InvalidEvaluableArgumentCountException(1, $this->evaluablesCount);
        }

        return !current($this->evaluables)->evaluate();
    }
}
