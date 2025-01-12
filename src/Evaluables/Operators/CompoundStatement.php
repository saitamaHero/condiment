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

    public function display(): string
    {
        $color = $this->evaluate() ?  "#007f5f55" : "#880d1e55";

        $evaluablesStr = join(
            "<p style=\"margin: 0;\">" . (preg_match("/conjunction/i", static::class) > 0 ? " AND " : " OR ") . "</p>",
            array_map('strval', $this->evaluables)
        );

        return "&nbsp;<span style=\" display: block; margin: 1em 0; padding: .15em .5em; border-radius: 1em; border: 1px solid #000; background: $color; \">(" . $evaluablesStr . ")" . "</span>";
    }
}