<?php

namespace Tests\Conditions;

use Condiment\Evaluables\Conditions\ConditionDefinition;

final class Magic extends ConditionDefinition
{
    public function execute(): bool {
        return false;
    }
}
