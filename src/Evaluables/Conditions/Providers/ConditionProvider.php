<?php

namespace Condiment\Evaluables\Conditions\Providers;


abstract class ConditionProvider
{
    /**
     * Get the condition classes that will be provided
     *
     * @return array<int,class-string>
     */
    public abstract function getDefinedConditions(): array;
}
