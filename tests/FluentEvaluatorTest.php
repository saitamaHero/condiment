<?php

namespace Tests;

use Condiment\Evaluables\Conditions\Definitions\Equals;
use Condiment\Evaluables\Evaluators\FluentEvaluator;
use PHPUnit\Framework\TestCase;

class FluentEvaluatorTest extends TestCase
{

     public function testConditionsCanBeAddedLikeMethods()
    {
        $evaluator = new FluentEvaluator();

        $evaluator->equals(1,1);

        $evaluables = $evaluator->getEvaluables();

        /**
         * @var \Condiment\Evaluables\Conditions\Conditions
         */
        $condition = $evaluables[0];

        $this->assertCount(1, $evaluables);
        $this->assertInstanceOf(Equals::class, $condition);
        $this->assertSame(1, $condition->getArgument(0));
        $this->assertSame(1, $condition->getArgument(0));
    }

    public function testConditionsCanBeChained()
    {
        $evaluator = new FluentEvaluator();

        $evaluator->equals(1,1)->contains('hello', 'el');

        $result = $evaluator->evaluate(false);

        // var_dump($evaluator->getEvaluables());

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testExceptionIsThrownIfANotExistingConditionIsCalled()
    {
        $this->expectException(\Condiment\Exceptions\EvaluatorInvalidConditionException::class);

        $evaluator = new FluentEvaluator();
        $evaluator->equality(1,1);
    }
}
