<?php 

namespace Condiment\Evaluables\Conditions;

use Condiment\Evaluables\Operators\Negation;

/**
 * [Description ConditionDefinition]
 */
abstract class ConditionDefinition extends Condition
{
    protected $options = [];

    /**
     * @var string|null
     */
    protected $logicalOperator = null;


    /**
     * @return Negation
     */
    public function negate(): Negation
    {
        return new Negation($this);
    }

    public static function getRegisterName()
    {
        return \mb_strtolower(\basename(\str_replace("\\", "/", static::class)));
    }

    public function __toString()
    {
        $result = $this->evaluate();

        $color = $result ? "#8ac926" : "#ba181b";

        return sprintf(
            "%s(%s) <span style=\"display: inline-block; width: .5rem; height: .5rem;  border: 1px solid #000; border-radius: 50%%; background: $color; \"></span>",
            static::getRegisterName(),
            join(", ", $this->arguments),
            // $result ? "true" : "false"
        );
    }
}
