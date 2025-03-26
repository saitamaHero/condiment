<?php

namespace Condiment\Evaluables\Conditions\Groups;

use Condiment\Evaluables\Evaluable;

abstract class ConditionGroup
{
    public abstract function group(array $conditions, array $connectors): Evaluable;
}
