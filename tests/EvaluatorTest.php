<?php

use Condiment\Evaluables\Evaluators\Evaluator;
use Condiment\Exceptions\EvaluatorInvalidConditionException;
use PHPUnit\Framework\TestCase;

final class EvaluatorTest extends TestCase
{
    private Evaluator $evaluator;

    protected function setUp(): void
    {
        $this->evaluator = new Evaluator();
    }

    // public function testEvaluatorCanNotBeEvaluatedWithoutConditions()
    // {
    //     $this->expectException(\Condiment\Exceptions\EvaluatorMissingConditionsException::class);
    //     $this->evaluator->evaluate();
    // }

    public static function conditionsProvider()
    {
        return [
            'Two conditions are evaluate and returns expected value' => [
                [
                    ['equals', [1,1], Evaluator::AND_CONNECTOR, false],
                    ['contains', ["Hello", "ll"], Evaluator::AND_CONNECTOR, false],
                ],
                true
            ],
            'Conditions are grouped correctly' => [
                [
                    ['equals', [1,2], Evaluator::AND_CONNECTOR, false],
                    ['contains', ["Hello", "b"], Evaluator::AND_CONNECTOR, false],
                    ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false],
                ],
                true
            ],
            'Groups are created manually' => [
                [
                    ['equals', [1,2], Evaluator::AND_CONNECTOR, false],
                    'and' => [
                        ['contains', ["Hello", "b"], Evaluator::AND_CONNECTOR, false],
                        ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false]
                    ],
                ],
                false
            ],
            'Nested groups can be added manually' => [
                [
                    ['equals', [1,2], Evaluator::AND_CONNECTOR, false],
                    'and' => [
                        ['contains', ["Hello", "b"], Evaluator::AND_CONNECTOR, false],
                        ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false],
                        'or' => [
                            ['endsWith', ["PHP", "HP"], Evaluator::AND_CONNECTOR, false],
                            ['startsWith', ["PHP", "PH"], Evaluator::OR_CONNECTOR, false],
                        ]
                    ],
                ],
                false
            ],
            'N groups can be added nested' => [
                [
                    ['equals', [1,2], Evaluator::AND_CONNECTOR, false],
                    'and' => [
                        ['contains', ["Hello", "b"], Evaluator::AND_CONNECTOR, false],
                        ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false],
                        'or' => [
                            ['endsWith', ["PHP", "HP"], Evaluator::AND_CONNECTOR, false],
                            ['startsWith', ["PHP", "PH"], Evaluator::OR_CONNECTOR, false],
                        ]
                    ],
                ],
                false
            ]

        ];
    }

    /**
     * @dataProvider conditionsProvider
     */
    public function testEvaluatorReturnsTrue(array $conditions, bool $expected)
    {
        $this->evaluator->addConditions($conditions);
        $this->assertSame($expected, $this->evaluator->evaluate());
    }

    public function testEvaluatorThrowsExceptionForNoExistingCondition ()
    {
        $this->expectException(EvaluatorInvalidConditionException::class);

        $this->evaluator->addCondition('equality', [9,9]);
    }
}
