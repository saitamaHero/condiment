<?php

namespace Condiment\Evaluables\Operators;

use Condiment\Evaluables;
use Condiment\Exceptions\InvalidEvaluableArgumentCountException;

abstract class CompoundStatement extends LogicalOperator
{
    public function evaluate(): bool
    {
        //@codeCoverageIgnoreStart
        if ($this->evaluablesCount < 2) {
            throw new InvalidEvaluableArgumentCountException(2, $this->evaluablesCount);
        }
        //@codeCoverageIgnoreEnd

        $evaluables = iterator_to_array($this->getEvaluables());

        return $this->runEvaluate(current($evaluables), $evaluables);
    }

    protected function runEvaluate(Evaluables\Evaluable $evaluable, array $evaluables)
    {
        return !($nextEvaluable = next($evaluables)) ?
            $evaluable->evaluate() :
            $this->compare($evaluable->evaluate(), fn() => $this->runEvaluate($nextEvaluable, $evaluables));
    }

    protected abstract function compare(bool $a, \Closure $next): bool;
}
