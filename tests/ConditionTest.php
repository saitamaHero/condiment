<?php

use Condiment\Evaluables\Conditions\Definitions\EndsWith;
use Condiment\Evaluables\Conditions\Definitions\Equals;
use Condiment\Evaluables\Conditions\Definitions\StartsWith;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{

    public static function conditionProvider()
    {
        return [
            'Equals: identical strings' => [Equals::class, ["condiment", "condiment"], true],
            'Equals: different strings' => [Equals::class, ["condiment", "spice"], false],
            'Equals: floating point issue' => [Equals::class, [0.1 + 0.2, 0.3], false],
            'StartsWith: valid prefix' => [StartsWith::class, ["condiment", "con"], true],
            'EndsWith: incorrect suffix' => [EndsWith::class, ["condiment", "con"], false],
        ];
    }

    /**
     * @dataProvider conditionProvider
     */
    public function testConditionEvaluatesToBoolean($conditionClass, array $args, bool $expected)
    {
        $condition = new $conditionClass;

        $result = $condition->args($args)->evaluate();

        $this->assertIsBool($result);
        $this->assertSame($expected, $result);
    }


    public function testConditionCannotBeEvaluatedWithoutArguments()
    {
        $this->expectException(Condiment\Exceptions\NoArgumentsException::class);

        $condition = new Equals();
        $condition->evaluate();
    }

    public function testConditionReturnsExpectedResult()
    {
        $this->assertTrue((new Equals())->args([1, 1])->evaluate());
        $this->assertFalse((new Equals())->args([1, 2])->evaluate());
    }

    public function testConditionCanBeNegated()
    {
        $negation = (new Equals())->args([1, 2])->negate();

        $this->assertInstanceOf(\Condiment\Evaluables\Operators\Negation::class, $negation);

        $this->assertTrue($negation->evaluate());
    }

    /**
     * @dataProvider negationProvider
     * @return void
     */
    public function testNegationReturnsTheOppositeResult($conditionClass, $args, $expected)
    {
        $condition = new $conditionClass;

        $negation = $condition->args($args)->negate();

        $this->assertInstanceOf(\Condiment\Evaluables\Operators\Negation::class, $negation);
        $this->assertNotSame($condition->evaluate(), $negation->evaluate(),  "Negation did not invert result correctly");
        $this->assertSame($expected, $negation->evaluate(), "Expected negated result mismatch");
    }

    public static function negationProvider()
    {
        return [
            'Equals: identical strings' => [Equals::class, ["condiment", "condiment"], false],
            'Equals: different strings' => [Equals::class, ["condiment", "HelloWorld"], true],
            'StartsWith: valid suffix' => [StartsWith::class, ["condiment", "con"], false],
            'EndsWith: invalid prefix' => [EndsWith::class, ["condiment", "con"], true],
        ];
    }

}

