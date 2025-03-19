<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions;

class LeaserThan extends Conditions\ConditionDefinition
{
    protected function execute(): bool
    {
        return $this->getArgument(0)< $this->getArgument(1);
    }
}
