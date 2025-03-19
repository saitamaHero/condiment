<?php

namespace Condiment\Evaluables\Conditions\Providers;

use Condiment\Evaluables\Conditions\Definitions;

final class DefaultConditionProvider extends ConditionProvider
{
    private $conditions = [
        'equals' => Definitions\Equals::class,
        'eq' => Definitions\Equals::class,
        'match' => Definitions\MatchPattern::class,
        'startsWith' => Definitions\StartsWith::class,
        'endsWith' => Definitions\EndsWith::class,
        'contains' => Definitions\Contains::class,
        'leaserThan' => Definitions\LeaserThan::class,
        'lt' => Definitions\LeaserThan::class,
        'greaterThan' => Definitions\GreaterThan::class,
        'gt' => Definitions\GreaterThan::class,
    ];


    public function getDefinedConditions(): array
    {
        return $this->conditions;
    }
}
