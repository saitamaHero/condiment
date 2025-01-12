<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions\ConditionDefinition;

class Equals extends ConditionDefinition
{
    protected function execute(): bool
    {
        list($a, $b) = $this->arguments;

        // $a = 270.10 + 20.10; $b = 290.20;

        // $a1 = sprintf("%.15f", $a);
        // $b1 = sprintf("%.15f", $b);

        // var_dump(is_float($a), compact('a1', 'b1'),abs(((float)$a-$b)/$b) < PHP_FLOAT_EPSILON, abs((($a-$b)) < PHP_FLOAT_EPSILON));
        // echo "$a === $b\n";

        // if (is_float($a) || is_float($b)) {
        //     $a = floatval($a);
        //     $b = floatval($b);

        //     return abs(($a-$b)/$b) < PHP_FLOAT_EPSILON;
        // }

        return $a === $b;
    }
}