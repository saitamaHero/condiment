<?php

namespace Condiment\Evaluables\Conditions\Groups;

use Condiment\Evaluables\Operators;
use Condiment\Evaluables\Evaluable;
use Condiment\Evaluables\Evaluators\Evaluator;

class AndThenOrGroup extends ConditionGroup
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
        $stack = [$evaluables[0]];

        for ($i = 0; $i < count($connectors); $i++) {
            $connector = strtolower($connectors[$i]);

            if (!isset($evaluables[$i + 1])) {
                throw new \LogicException("An evaluable is missing for the connector at position {$i}.");
            }

            if ($connector === Evaluator::AND_CONNECTOR) {
                $stack[count($stack) - 1] = Operators\Conjunction::create($stack[count($stack) - 1], $evaluables[$i + 1]);
            } elseif ($connector === Evaluator::OR_CONNECTOR) {
                $stack[] = $evaluables[$i + 1];
            } else {
                throw new \InvalidArgumentException("Illegal Connector: \"{$connector}\".");
            }
        }

        return count($stack) > 1 ? new Operators\Disjunction(...$stack) : reset($stack);
    }
}
