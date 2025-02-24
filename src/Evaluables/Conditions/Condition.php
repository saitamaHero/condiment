<?php

namespace Condiment\Evaluables\Conditions;

use Condiment\Evaluables\Evaluable;
use Condiment\Evaluables\Negable;
use Condiment\Evaluables\Operators\Negation;
use Condiment\Exceptions\NoArgumentsException;

abstract class Condition implements Evaluable, Negable
{
    protected $arguments = [];

    public function args(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

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
        if (empty($this->arguments)) {
            throw new NoArgumentsException(static::class);
        }

        return $this->execute();
    }
}
