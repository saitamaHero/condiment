<?php

namespace Condiment\Evaluables\Evaluators;

/**
 * Fluent Evaluator provides a fluent API to
 * chain calls in more "fluent" way
 *
 */
class FluentEvaluator extends Evaluator
{
    public function __call($name, $arguments)
    {
        //TODO maybe I should evaluate that any condition added to this method to
        // enforce the conditions, but the condition itself handles the error
        $conditionName = $this->getConditionNameFromCall($name);

        if (! method_exists($this, $conditionName) && key_exists($conditionName, $this->conditionDefinitions)) {
            $connector = strpos($name, self::OR_CONNECTOR) !== 0 ? self::AND_CONNECTOR : self::OR_CONNECTOR;
            $negate = stripos($name, self::NOT_CONNECTOR) !== false;

            $this->addCondition($conditionName, $arguments, $connector, $negate);
        } else {
            throw new \Exception("Illegal Method \"$name\" on " . static::class);
        }

        return $this;
    }

    protected function getConditionNameFromCall(string $methodName): string
    {
        return str_ireplace([self::OR_CONNECTOR, self::NOT_CONNECTOR], "", lcfirst($methodName));
    }
}
