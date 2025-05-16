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
        return \lcfirst(\basename(\str_replace("\\", "/", static::class)));
    }
}
