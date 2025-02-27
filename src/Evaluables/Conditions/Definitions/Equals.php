<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions;

class Equals extends Conditions\ConditionDefinition
{
    protected $stringCompareFunction = 'strcasecmp';

    protected function execute(): bool
    {
        list($a, $b) = $this->arguments;

        if (is_numeric($a) && is_numeric($b)) {
            return $this->numberEquals((float)$a, (float)$b);
        }else if ($a instanceof \DateTimeInterface && $b instanceof \DateTimeInterface) {
            return $this->dateTimeEquals($a, $b);
        }

        return call_user_func_array($this->stringCompareFunction, [(string)$a, (string)$b]) === 0;
    }


    public function numberEquals(float $a, float $b, $epsilon = 0.00001): bool
    {
        return abs($a - $b) < $epsilon;
    }

    public function dateTimeEquals(\DateTimeInterface $a, \DateTimeInterface $b): bool {
        return $a->format('Y-m-d H:i:s.u') === $b->format('Y-m-d H:i:s.u');
    }
}
