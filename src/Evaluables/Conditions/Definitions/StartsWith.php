<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions;

class StartsWith extends Conditions\ConditionDefinition
{
    use Conditions\Regexable;

    protected function execute(): bool
    {
        list($subject, $regex) = $this->arguments;

        $regex = $this->sanitizeRegex($regex);

        return $this->matchInput($subject, "^$regex") > 0;
    }
}
