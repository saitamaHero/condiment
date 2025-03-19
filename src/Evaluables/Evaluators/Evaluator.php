<?php

namespace Condiment\Evaluables\Evaluators;

use Condiment\Evaluables\{Evaluable, Operators};

class Evaluator
{
    const AND_CONNECTOR = 'and';

    const OR_CONNECTOR = 'or';

    const NOT_CONNECTOR = 'not';

    /**
     * @var array<int,Evaluable>
     */
    protected array $evaluables = [];

    /**
     * @var array
     */
    protected array $conditionDefinitions = [];

    /**
     * Undocumented variable
     *
     * @var array<int,
     */
    protected $conditionProviders = [];

    /**
     * @var array<int, 'and' | 'or'>
     */
    protected $connectors = [];

    public function __construct(array $conditionProviders = [])
    {
        if (empty($conditionProviders)) {
            $this->conditionProviders[] = new \Condiment\Evaluables\Conditions\Providers\DefaultConditionProvider();
        }

        $this->defineConditions();
    }

    public function groupConditions(array $evaluables, array $connectors): Evaluable //TODO: rename it to groupEvaluables
    {
        $stack = [];

        $current = $evaluables[0];

        for ($i = 0; $i < count($connectors); $i++) {
            $connector = strtolower($connectors[$i]);

            if ($connector === self::AND_CONNECTOR) {
                if (key_exists($i + 1, $evaluables)) {
                    $current = Operators\Conjunction::create($current, $evaluables[$i + 1]);
                } else {
                    $stack[] = $current;
                }
            } else if ($connector === self::OR_CONNECTOR) {
                $stack[] = $current;
                $current = $evaluables[$i + 1];
            } else {
                throw new \Exception("Illegal Connector \"{$connector}\"");
            }
        }

        $stack[] = $current;

        if (count($stack) < 2) {
            return array_shift($stack);
        }

        return in_array(self::OR_CONNECTOR, $connectors) ? new Operators\Disjunction(...$stack) : new Operators\Conjunction(...$stack);
    }

    public function getEvaluable()
    {
        return $this->groupConditions($this->evaluables, $this->connectors);
    }

    public function evaluate(bool $reset = true): bool
    {

        if (count($this->evaluables) < 1) {
            return false;
        }

        $evaluable = $this->getEvaluable();

        echo '<pre style="display: block; max-width: 500px;">';
        echo strval($evaluable);
        echo "</pre>";

        if ($reset) {
            $this->reset();
        }

        return $evaluable->evaluate();
    }

    /**
     * @param string $conditionDefinitionClass
     *
     * @return void
     */
    public function defineFromClass(string $conditionDefinitionClass)
    {
        $this->defineCondition(
            $conditionDefinitionClass::getRegisterName(),
            $conditionDefinitionClass
        );
    }

    /**
     * @param string $name
     * @param class-string $condition
     *
     * @return void
     */
    public function defineCondition(string $name, string $definition)
    {
        $this->conditionDefinitions[$name] = $definition;
    }

    /**
     * @param string $conditionDefinitionClass
     * @param array $args
     * @param bool $negate
     * @return Evaluable
     * @throws \Exception
     */
    protected function initDefinition(string $conditionDefinitionClass, array $args, bool $negate = false)
    {
        $definition = new \ReflectionClass($conditionDefinitionClass);

        $condition = $definition->newInstance()->args($args);

        if ($negate) {
            return $condition->negate();
        }

        return $condition;
    }

    public function addEvaluable(Evaluable $evaluable, string $connector = 'and')
    {

        if (count($this->evaluables) > 0) {

            $this->connectors[] = $connector;
        }

        $this->evaluables[] = $evaluable;
    }

    public function addEvaluablesFromArray(array $evaluables)
    {
        foreach ($evaluables as $evaluable) {

            if (isset($evaluable['type']) && $evaluable['type'] === 'group') {
                $conditions = $evaluable['conditions'] ?? [];

                $this->_group(
                    fn($evaluator) => $evaluator->addEvaluablesFromArray($conditions),
                    $evaluable['connector'] ?? self::AND_CONNECTOR
                );
            } else {

                $this->addCondition(
                    $evaluable["condition"],
                    $evaluable["arguments"],
                    $evaluable["connector"] ?? self::AND_CONNECTOR,
                    boolval($evaluable["negate"])
                );
            }
        }
    }

    public function addCondition(string $condition, array $args, $connector = self::AND_CONNECTOR, bool $negate = false)
    {
        // echo '<pre>';
        // var_dump($this->getDefinedConditions());
        // echo '</pre>';
        // die();
        $condition = $this->initDefinition(
            $this->conditionDefinitions[$condition],
            $args,
            $negate
        );

        $this->addEvaluable($condition, $connector);

        return $this;
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
     * @param callable $closure
     *
     * @return $this
     */
    public function orGroup(\Closure $closure) //TODO maybe this can be part of fluent evaluator
    {
        return $this->_group($closure, self::OR_CONNECTOR);
    }

    public function addGroup(array $conditions, string $connector = self::AND_CONNECTOR, $negate = false)
    {
        return $this->_group(function ($evaluator) use ($conditions) {
            foreach ($conditions as $key => $condition) {
                if (!is_array($condition[0])) {
                    $evaluator->addCondition(...$condition);
                }else {
                  $evaluator->addGroup($condition, (string)$key);
                }

            }
        }, $connector, $negate);
    }

    public function addConditions(array $conditions)
    {
        foreach ($conditions as $key => $condition) {

            if (!is_array($condition[0])) {
                $this->addCondition(...$condition);
            }else {

                $groupConnector = Evaluator::AND_CONNECTOR;

                if (is_string($key) && in_array($key, [Evaluator::AND_CONNECTOR, Evaluator::OR_CONNECTOR])) {
                    $groupConnector = \strtolower($key);
                }

                $this->addGroup($condition, $groupConnector);
            }
        }
    }

    protected function _group(\Closure $closure, string $connector, bool $negate = false)
    {
        $evaluator = clone $this;

        $evaluable = $closure($evaluator);

        if (! ($evaluable instanceof Evaluable)) {
            $evaluable = $evaluator->getEvaluable();
        }

        if ($negate) {
            $evaluable = new Negation($evaluable);
        }

        $this->addEvaluable($evaluable, $connector);

        return $this;
    }

    public function reset()
    {
        $this->evaluables = [];
        $this->connectors = [];
    }

    public function conditionExists(string $conditionName)
    {
        return key_exists($conditionName, $this->conditionDefinitions);
    }

    public function __clone()
    {
        $this->reset();
    }

    public function getDefinedConditions()
    {
        return $this->conditionDefinitions;
    }

    protected function defineConditions()
    {
        $conditions = array_reduce(
            $this->conditionProviders,
            function ($conditions, \Condiment\Evaluables\Conditions\Providers\ConditionProvider $conditionProvider) {
                return array_merge($conditions, $conditionProvider->getDefinedConditions());
            },
            []
        );

        foreach ($conditions as $key => $condition) {

            # string keys might indicate that is the name/alias of the condition
            if (is_string($key)) {
                $this->defineCondition($key, $condition);
            }else {
                $this->defineFromClass($condition);
            }
        }
    }
}
