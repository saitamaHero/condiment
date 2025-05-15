<?php

namespace Condiment\Evaluables\Operators;

use Condiment\Evaluables;

abstract class LogicalOperator implements Evaluables\Evaluable, Evaluables\Negable
{
    protected $evaluables = [];

    protected $evaluablesCount = 0;

    public function __construct(...$evaluables)
    {
        if (!empty($evaluables)) {
            $this->setEvaluables(...$evaluables);
        }
    }

    public function getEvaluables(): iterable
    {
        foreach ($this->evaluables as $key => $evaluable) {
            yield $evaluable;
        }
    }

    public function setEvaluables(...$evaluables)
    {
        $this->evaluables = $evaluables;

        $this->updateEvaluablesCount();
    }

    protected function updateEvaluablesCount()
    {
        $this->evaluablesCount = count($this->evaluables);
    }

    public static function __callStatic($name, $arguments)
    {
        if ($name === 'create') {
            return new static(...$arguments);
        }
        // Negatione: value of $name is case sensitive.
        // echo "Calling static method '$name' "
        //      . implode(', ', $arguments). "\n";
    }

    public function negate(): Negation
    {
        return new Negation($this);
    }
}
