<?php

namespace Condiment\Evaluables\Evaluators;

use Condiment\Evaluables\{Evaluable, Operators};
use Condiment\Evaluables\Conditions\Groups\AndThenOrGroup;
use Condiment\Evaluables\Conditions\Groups\ConditionGroup;
use Condiment\Evaluables\Operators\Negation;
use Condiment\Exceptions\EvaluatorInvalidConditionException;

class Evaluator
{
    const AND_CONNECTOR = 'and';

    const OR_CONNECTOR = 'or';

    const NOT_CONNECTOR = 'not';

    /**
     * Prefix for identify which argument should be pull
     * from the datasource
     *
     * @var string
     */
    const ARGUMENT_RESOLVE_PREFIX_TOKEN = "@@";

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

    /**
     * @var array
     */
    protected $datasource = [];

    //TODO resolve grouping manually with a callback or special class that groups evaluables

    /**
     * @var \Closure|ConditionGroup
     */
    protected $conditionGroup;

    public function __construct(array $conditionProviders = [])
    {
        if (empty($conditionProviders)) {
            $this->conditionProviders[] = new \Condiment\Evaluables\Conditions\Providers\DefaultConditionProvider();
        }

        $this->defineConditions();

        $this->setConditionGroup(new AndThenOrGroup());
    }

    public function setDataSource(array $datasource)
    {
        $this->datasource = $this->flattenDataSource($datasource);

        return $this;
    }

    public function setConditionGroup($conditionGroup)
    {
        $this->conditionGroup = $conditionGroup;

        return $this;
    }

    public function groupConditions(array $evaluables, array $connectors): Evaluable
    {
        if ($this->conditionGroup instanceof \Closure) {
            return $this->conditionGroup->call($this, $evaluables, $connectors);
        }

        return $this->conditionGroup->group($evaluables, $connectors);
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

    public function addCondition(string $condition, array $args, $connector = self::AND_CONNECTOR, bool $negate = false)
    {
        if (!$this->conditionExists($condition)) {
            throw new EvaluatorInvalidConditionException($condition);
        }

        $condition = $this->initDefinition(
            $this->conditionDefinitions[$condition],
            $this->resolveArguments($args),
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

        $this->setConditionGroup(new AndThenOrGroup());
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

    protected function getFromDataSource(string $key)
    {
        if (!key_exists($key, $this->datasource)) {
            return null;
        }

        return $this->datasource[$key];
    }

    /**
     * @param mixed $datasource
     * @param string $prefix
     *
     * @return array
     */
    protected function flattenDataSource(array $datasource, $prefix = '') {
        $flatten = [];

        foreach ($datasource as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                // If the value is an array, recurse into it
                $flatten = array_merge($flatten, $this->flattenDataSource($value, $newKey));
            } else {
                // Otherwise, set the value in the result array
                $flatten[$newKey] = $value;
            }
        }

        return $flatten;
    }

    protected function resolveArguments(array $arguments)
    {
        return array_map(
            function ($argument) {
                if (is_string($argument) && mb_strpos($argument, self::ARGUMENT_RESOLVE_PREFIX_TOKEN) === 0) {
                    $argument = $this->getFromDataSource(
                        trim(str_replace(self::ARGUMENT_RESOLVE_PREFIX_TOKEN, "", $argument))
                    );
                }

                return $argument;
            },
            $arguments
        );
    }
}
