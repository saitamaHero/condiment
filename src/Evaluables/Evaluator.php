<?php 

namespace Condiment\Evaluables;

use Condiment\Evaluables\Conditions\Definitions;
use Condiment\Evaluables\Operators\Conjunction;
use Condiment\Evaluables\Operators\Disjunction;

class Evaluator
{
    /**
     * @var array<int,Evaluable>
     */
    protected array $evaluables = [];

    /**
     * @var array
     */
    protected array $conditionDefinitions = [
        'equals' => Definitions\Equals::class,
        'startswith' => Definitions\StartsWith::class,
        'endswith' => Definitions\EndsWith::class,
        'match' => Definitions\MatchPattern::class,
    ];

    /**
     * @var array<int, 'and' | 'or'>
     */
    protected $connectors = [];

    public function groupConditions(array $evaluables, array $connectors): Evaluable //TODO: rename it to groupEvaluables
    {
        $stack = [];

        $current = $evaluables[0];

        for ($i = 0; $i < count($connectors); $i++) {
            $connector = $connectors[$i];

            if ($connector === 'and') {
                if (key_exists($i + 1, $evaluables)) {
                    $current = Conjunction::create($current, $evaluables[$i + 1]);
                } else {
                    $stack[] = $current;
                }
            } else if ($connector === 'or') {
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

        return in_array('or', $connectors) ? Disjunction::create(...$stack) : Conjunction::create(...$stack);
    }

    public function getEvaluable()
    {
        return $this->groupConditions($this->evaluables, $this->connectors);
    }

    public function evaluate(): bool
    {

        if (count($this->evaluables) < 1) {
            return false;
        }

        $evaluable = $this->getEvaluable();

        echo '<pre style="display: block; max-width: 500px;">';
        echo strval($evaluable);
        echo "</pre>";

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
                    fn ($evaluator) => $evaluator->addEvaluablesFromArray($conditions),
                    $evaluable['connector'] ?? 'and'
                );
            }else {

                $name = $evaluable["condition"];
    
                $condition = $this->initDefinition(
                    $this->conditionDefinitions[$name],
                    $evaluable["arguments"],
                    boolval($evaluable["negate"])
                );
            }

            $this->addEvaluable($condition, $evaluable["connector"] ?? "and");
        }
    }

    public function __call($name, $arguments)
    {
        $conditionName = str_replace(["or", "not"], "", mb_strtolower($name));

        if (
            ! method_exists($this, $conditionName) &&
            key_exists($conditionName, $this->conditionDefinitions)
        ) {
            $condition = $this->initDefinition(
                $this->conditionDefinitions[$conditionName],
                $arguments,
                stripos($name, "not") !== false
            );

            $this->addEvaluable($condition, strpos($name, "or") !== 0 ? "and" : "or");

            return $this;
        } else {
            throw new \Exception("Illegal Method \"$name\" on " . static::class);
        }
    }

    /**
     * @param callable $closure
     * 
     * @return Evaluable
     */
    public function group(\Closure $closure)
    {
        return $this->_group($closure, 'and');
    }

    /**
     * @param callable $closure
     * 
     * @return Evaluable
     */
    public function orGroup(\Closure $closure)
    {
        return $this->_group($closure, 'or');
    }

    protected function _group(\Closure $closure, string $connector)
    {
        $evaluator = clone $this;

        $evaluable = $closure($evaluator);

        if (! ($evaluable instanceof Evaluable)) {
            $evaluable = $evaluator->getEvaluable();
        }

        $this->addEvaluable($evaluable, $connector);

        return $this;
    }

    public function __clone()
    {
        $this->evaluables = [];
        $this->connectors = [];
    }
}