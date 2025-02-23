<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions\ConditionDefinition;

class EndsWith extends ConditionDefinition
{

    protected function execute(): bool
    {
        list($a, $b) = $this->arguments;


        return preg_match("/$b$/", $a) > 0;
    }
}
