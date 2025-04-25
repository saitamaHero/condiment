<?php

namespace Condiment\Evaluables\Conditions\Groups;

use Condiment\Evaluables\Operators;
use Condiment\Evaluables\Evaluable;
use Condiment\Evaluables\Evaluators\Evaluator;

class AndGroup extends ConditionGroup
{
    /**
     * @param array $evaluables
     * @param array $connectors
     *
     * @return Evaluable
     * @throws \InvalidArgumentException
     */
    public function group(array $evaluables, array $connectors): Evaluable
    {
        if (count($evaluables) < 2) {
            return current($evaluables);
        }

        return new Operators\Conjunction(...$evaluables);
    }
}
