<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions;

class Contains extends Conditions\ConditionDefinition
{
    use Conditions\Regexable;

    protected function execute(): bool
    {
        list($subject, $input) = $this->arguments;

        $regex = $this->sanitizeRegex($input);

        return $this->matchInput($subject, "$regex") > 0;
    }
}
