<?php

namespace Condiment\Evaluables\Evaluators;

/**
 * Fluent Evaluator can handle the add evaluables from
 * a more natural programming way.
 *
 */
class FluentEvaluator extends Evaluator
{
    public function __call($name, $arguments)
    {
        $conditionName = str_ireplace([self::OR_CONNECTOR, self::NOT_CONNECTOR], "", mb_strtolower($name));

        if (
            ! method_exists($this, $conditionName) &&
            key_exists($conditionName, $this->conditionDefinitions)
        ) {
            $condition = $this->initDefinition(
                $this->conditionDefinitions[$conditionName],
                $arguments,
                stripos($name, self::NOT_CONNECTOR) !== false
            );

            $this->addEvaluable(
                $condition,
                strpos($name, self::OR_CONNECTOR) !== 0 ? self::AND_CONNECTOR : self::OR_CONNECTOR
            );

        } else {
            throw new \Exception("Illegal Method \"$name\" on " . static::class);
        }

        return $this;
    }
}
