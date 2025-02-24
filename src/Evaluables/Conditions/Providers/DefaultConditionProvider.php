<?php

namespace Condiment\Evaluables\Conditions\Providers;

use Condiment\Evaluables\Conditions\Definitions;

final class DefaultConditionProvider extends ConditionProvider
{
    private $conditions = [
        Definitions\Equals::class,
        Definitions\MatchPattern::class,
        Definitions\StartsWith::class,
        Definitions\EndsWith::class,
    ];


    public function getDefinedConditions(): array
    {
        return $this->conditions;
    }
}
