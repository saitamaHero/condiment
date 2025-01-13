<?php

use Condiment\Evaluables\Conditions\Definitions\Equals;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
 
    public function test_condition_can_be_evaluated()
    {
        $condition = new Equals();

        $result = $condition->args([1, 1])->evaluate();

        $this->assertIsBool($result);
    }


    public function test_condition_cannot_be_evaluated_without_arguments()
    {
        $this->expectException(Condiment\Exceptions\NoArgumentsException::class);

        (new Equals())->evaluate();
    }
}
