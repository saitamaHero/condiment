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

    public function test_condition_returns_expected_result() 
    {
        $this->assertTrue((new Equals())->args([1, 1])->evaluate());
        $this->assertFalse((new Equals())->args([1, 2])->evaluate());
    }

    public function test_condition_can_be_negated()
    {
        $negation = (new Equals())->args([1, 2])->negate();

        $this->assertInstanceOf(\Condiment\Evaluables\Operators\Negation::class, $negation);
    }

    public function test_negation_returns_the_opposite_result()
    {
        $condition = new Equals();
        $condition->args(["condiment", "condiment"]);

        $this->assertEquals(true, $condition->evaluate());
        $this->assertEquals(false, $condition->negate()->evaluate());

        $condition = new Equals();
        $condition->args(["Hello World", "condiment"]);

        $this->assertEquals(false, $condition->evaluate());
        $this->assertEquals(true, $condition->negate()->evaluate());

        
    }

}

