<?php 

namespace Condiment\Evaluables\Conditions;

use Condiment\Evaluables\Evaluable;
use Condiment\Evaluables\Negable;
use Condiment\Evaluables\Operators\Negation;

abstract class Condition implements Evaluable, Negable
{
    /**
     * @return bool
    */
    protected abstract function execute(): bool;

    public function negate(): Negation
    {
        return new Negation($this);
    }

    public function evaluate(): bool
    {
        return $this->execute();
    }
}
