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
        $tokens = $this->getTokens($name);

        return  $this->addCondition(
            $tokens['condition'],
            $arguments,
            $tokens['connector'],
            $tokens['negation']
        );;
    }

    /**
     * Get the needed tokens from the method call made by magic method
     *
     * This returns any modifies applied to the condition for example
     *
     * orNotContains returns connector => 'or', negation => true, condition=> 'contains'
     *
     * AND connector is assumed by default to avoid things like andEquals, andNotEquals
     * this conditions pass as equals(x, x) notEquals(x,x) for better verbosity
     *
     * @param string $methodName
     *
     * @return array
     */
    public function getTokens(string $methodName)
    {
        $conditionName = \str_ireplace(
            [self::OR_CONNECTOR, self::NOT_CONNECTOR],
            "",
            $methodName
        );

        $chunks = array_map(
            'strtolower',
            preg_split("/(?=[A-Z])/", str_replace($conditionName, "", $methodName))
        );

        $chunkCount = count($chunks);

        if ($chunkCount > 1) {
            if (!in_array($chunks[0], [self::AND_CONNECTOR, self::OR_CONNECTOR])) {
                //TODO work on a new Exception
                throw new \Exception("Connectors AND, OR should goes first when used combined with NOT");
            }
        }

        $tokens = [
            'negation' => in_array(self::NOT_CONNECTOR, $chunks),
            'connector' => in_array(self::OR_CONNECTOR, $chunks) ? self::OR_CONNECTOR : self::AND_CONNECTOR,
            'condition' =>  \lcfirst($conditionName),
        ];

        return $tokens;
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function group(\Closure $closure)
    {
        return $this->_group($closure, self::AND_CONNECTOR);
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function orGroup(\Closure $closure) //TODO maybe this can be part of fluent evaluator
    {
        return $this->_group($closure, self::OR_CONNECTOR);
    }
}
