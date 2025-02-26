<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions\ConditionDefinition;

class Equals extends ConditionDefinition
{
    protected $stringCompareFunction = 'strcasecmp';

    protected function execute(): bool
    {
        list($a, $b) = $this->arguments;

        if (is_numeric($a) && is_numeric($b))
        {
            return $this->numberEquals((float)$a, (float)$b);
        }

        return call_user_func_array($this->stringCompareFunction, [$a, $b]) === 0;
    }


    public function numberEquals(float $a, float $b, $epsilon = 0.00001)
    {
        return abs($a - $b) < $epsilon;
    }
}
