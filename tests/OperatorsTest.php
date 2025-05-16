<?php
namespace Tests;

use Condiment\Evaluables\Conditions\Definitions\Equals;
use Condiment\Evaluables\Conditions\Definitions\GreaterThan;
use Condiment\Evaluables\Operators\Negation;
use Condiment\Exceptions\InvalidEvaluableArgumentCountException;
use PHPUnit\Framework\TestCase;

class OperatorsTest extends TestCase
{
    public function testNegationThrowsExceptionOnInvalidArgumentCount()
    {
        $negation = new Negation();

        $this->expectException(InvalidEvaluableArgumentCountException::class);

        $negation->evaluate();
    }
    public function testNegationThrowsExceptionOnMoreThanOneArgument()
    {
        $equalCondition = new Equals();
        $equalCondition->args([1,2]);

        $gtCondition = new GreaterThan();
        $gtCondition->args([1,2]);

        $negation = new Negation(
            $equalCondition,
            $gtCondition
        );

        $this->expectException(InvalidEvaluableArgumentCountException::class);

        $negation->evaluate();
    }
}
