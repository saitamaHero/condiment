<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions;

class GreaterThan extends Conditions\ConditionDefinition
{
    protected function execute(): bool
    {
        return $this->getArgument(0) > $this->getArgument(1);
    }
}
