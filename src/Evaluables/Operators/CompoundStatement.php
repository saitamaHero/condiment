<?php

namespace Condiment\Evaluables\Operators;

use Condiment\Evaluables;

abstract class CompoundStatement extends LogicalOperator
{
    public function evaluate(): bool
    {
        if ($this->evaluablesCount < 2) {
            throw new \Exception("Invalid conditions to evaluate");
        }

        $evaluables = iterator_to_array($this->getEvaluables());

        return $this->runEvaluate(current($evaluables), $evaluables);
    }

    protected function runEvaluate(Evaluables\Evaluable $evaluable, array $evaluables)
    {
        return !($nextEvaluable = next($evaluables)) ?
            $evaluable->evaluate() :
            $this->compare($evaluable->evaluate(), fn() => $this->runEvaluate($nextEvaluable, $evaluables));
    }


    public function addEvaluables(Evaluables\Evaluable ...$evaluable)
    {
        $this->evaluables = array_merge($this->evaluables, $evaluable);
    }

    protected abstract function compare(bool $a, \Closure $next): bool;
}
