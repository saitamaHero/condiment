<?php

namespace Tests;

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

    public static function conditionsProvider()
    {
        return [
            'Two conditions are evaluate and returns expected value' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 1],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'condition',
                        'condition' => 'contains',
                        'args' => ["Hello", "ll"],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                ],
                true
            ],
            'Conditions are grouped correctly' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 2],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'condition',
                        'condition' => 'contains',
                        'args' => ["Hello", "b"],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'condition',
                        'condition' => 'match',
                        'args' => ["condition", "tio(n|nal)"],
                        'connector' => Evaluator::OR_CONNECTOR,
                        'negate' => false
                    ],
                ],
                true
            ],
            'Groups are created manually' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 2],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'group',
                        'conditions' => [
                            [
                                'type' => 'condition',
                                'condition' => 'contains',
                                'args' => ["Hello", "b"],
                                'connector' => Evaluator::AND_CONNECTOR,
                                'negate' => false
                            ],
                            [
                                'type' => 'condition',
                                'condition' => 'match',
                                'args' => ["condition", "tio(n|nal)"],
                                'connector' => Evaluator::OR_CONNECTOR,
                                'negate' => false
                            ]
                        ],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                ],
                false
            ],
            'Nested groups can be added manually' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 2],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'group',
                        'conditions' => [
                            [
                                'type' => 'condition',
                                'condition' => 'contains',
                                'args' => ["Hello", "b"],
                                'connector' => Evaluator::AND_CONNECTOR,
                                'negate' => false
                            ],
                            [
                                'type' => 'condition',
                                'condition' => 'match',
                                'args' => ["condition", "tio(n|nal)"],
                                'connector' => Evaluator::OR_CONNECTOR,
                                'negate' => false
                            ],
                            [
                                'type' => 'group',
                                'conditions' => [
                                    [
                                        'type' => 'condition',
                                        'condition' => 'endsWith',
                                        'args' => ["PHP", "HP"],
                                        'connector' => Evaluator::AND_CONNECTOR,
                                        'negate' => false
                                    ],
                                    [
                                        'type' => 'condition',
                                        'condition' => 'startsWith',
                                        'args' => ["PHP", "PH"],
                                        'connector' => Evaluator::OR_CONNECTOR,
                                        'negate' => false
                                    ]
                                ],
                                'connector' => Evaluator::OR_CONNECTOR,
                                'negate' => false
                            ]
                        ],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                ],
                false
            ],
            'N groups can be added nested' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 2],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'group',
                        'conditions' => [
                            [
                                'type' => 'condition',
                                'condition' => 'contains',
                                'args' => ["Hello", "b"],
                                'connector' => Evaluator::AND_CONNECTOR,
                                'negate' => false
                            ],
                            [
                                'type' => 'condition',
                                'condition' => 'match',
                                'args' => ["condition", "tio(n|nal)"],
                                'connector' => Evaluator::OR_CONNECTOR,
                                'negate' => false
                            ],
                            [
                                'type' => 'group',
                                'conditions' => [
                                    [
                                        'type' => 'condition',
                                        'condition' => 'endsWith',
                                        'args' => ["PHP", "HP"],
                                        'connector' => Evaluator::AND_CONNECTOR,
                                        'negate' => false
                                    ],
                                    [
                                        'type' => 'condition',
                                        'condition' => 'startsWith',
                                        'args' => ["PHP", "PH"],
                                        'connector' => Evaluator::OR_CONNECTOR,
                                        'negate' => false
                                    ]
                                ],
                                'connector' => Evaluator::OR_CONNECTOR,
                                'negate' => false
                            ]
                        ],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                ],
                false
            ],
            'Deeply nested conditions' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 1],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ],
                    [
                        'type' => 'group',
                        'conditions' => [
                            [
                                'type' => 'condition',
                                'condition' => 'contains',
                                'args' => ["Hello", "H"],
                                'connector' => Evaluator::AND_CONNECTOR,
                                'negate' => false
                            ],
                            [
                                'type' => 'group',
                                'conditions' => [
                                    [
                                        'type' => 'condition',
                                        'condition' => 'match',
                                        'args' => ["condition", "tio(n|nal)"],
                                        'connector' => Evaluator::OR_CONNECTOR,
                                        'negate' => false
                                    ],
                                    [
                                        'type' => 'group',
                                        'conditions' => [
                                            [
                                                'type' => 'condition',
                                                'condition' => 'endsWith',
                                                'args' => ["PHP", "HP"],
                                                'connector' => Evaluator::AND_CONNECTOR,
                                                'negate' => false
                                            ],
                                            [
                                                'type' => 'group',
                                                'conditions' => [
                                                    [
                                                        'type' => 'condition',
                                                        'condition' => 'startsWith',
                                                        'args' => ["PHP", "PH"],
                                                        'connector' => Evaluator::OR_CONNECTOR,
                                                        'negate' => false
                                                    ],
                                                    [
                                                        'type' => 'group',
                                                        'conditions' => [
                                                            [
                                                                'type' => 'condition',
                                                                'condition' => 'gt',
                                                                'args' => [10, 5],
                                                                'connector' => Evaluator::AND_CONNECTOR,
                                                                'negate' => false
                                                            ],
                                                            [
                                                                'type' => 'condition',
                                                                'condition' => 'lt',
                                                                'args' => [5, 10],
                                                                'connector' => Evaluator::AND_CONNECTOR,
                                                                'negate' => false
                                                            ]
                                                        ],
                                                        'connector' => Evaluator::AND_CONNECTOR,
                                                        'negate' => false
                                                    ]
                                                ],
                                                'connector' => Evaluator::OR_CONNECTOR,
                                                'negate' => false
                                            ]
                                        ],
                                        'connector' => Evaluator::AND_CONNECTOR,
                                        'negate' => false
                                    ]
                                ],
                                'connector' => Evaluator::OR_CONNECTOR,
                                'negate' => false
                            ]
                        ],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => false
                    ]
                ],
                true
            ],
            'Negated condition returns true' => [
                [
                    [
                        'type' => 'condition',
                        'condition' => 'equals',
                        'args' => [1, 2],
                        'connector' => Evaluator::AND_CONNECTOR,
                        'negate' => true
                    ]
                ],
                true
            ],
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

    public function testEvaluatorThrowsExceptionForNoExistingCondition()
    {
        $this->expectException(EvaluatorInvalidConditionException::class);

        $this->evaluator->addCondition('equality', [9, 9]);
    }

    public function testEvaluatorCanResolveArgumentFromDataSource()
    {
        $product = [
            'id' => 1,
            'name' => 'Ultra HD 4K Television',
            'price' => 799.99,
            'category' => 'Electronics',
            'brand' => 'SuperVision',
            'model' => 'SV-4K2023',
            'screenSize' => '65 inches',
            'resolution' => '3840 x 2160',
            'features' => [
                'Smart TV',
                'HDR Support',
                'Voice Control',
                'Wi-Fi Enabled',
                'Multiple HDMI Ports',
            ],
            'warranty' => '2 years',
            'ratings' => [
                'average' => 4.7,
                'totalReviews' => 150,
            ],
        ];

        $this->evaluator->setDataSource($product)
            ->addConditions([
                [
                    'type' => 'condition',
                    'condition' => 'gt',
                    'args' => [
                        '@@ratings.totalReviews',
                        100
                    ],
                    'connector' => Evaluator::AND_CONNECTOR,
                    'negate' => false
                ],
                [
                    'type' => 'condition',
                    'condition' => 'contains',
                    'args' => [
                        '@@resolution',
                        '2160'
                    ],
                    'connector' => Evaluator::AND_CONNECTOR,
                    'negate' => false
                ]
            ]);

        $this->assertTrue($this->evaluator->evaluate());
    }

    public function testNoErrorIsReturnIfArgumentIsNotFound()
    {
        $product = [
            'id' => 1,
            'name' => 'Ultra HD 4K Television',
            'price' => 799.99,
            'category' => 'Electronics',
            'brand' => 'SuperVision',
            'model' => 'SV-4K2023',
            'screenSize' => '65 inches',
            'resolution' => '3840 x 2160',
            'features' => [
                'Smart TV',
                'HDR Support',
                'Voice Control',
                'Wi-Fi Enabled',
                'Multiple HDMI Ports',
            ],
            'warranty' => '2 years',
            'ratings' => [
                'average' => 4.7,
                'totalReviews' => 150,
            ],
        ];

        $this->evaluator->setDataSource($product)
            ->addConditions([
                [
                    'type' => 'condition',
                    'condition' => 'gt',
                    'args' => [
                        '@@ratings.missingProperty',
                        100
                    ],
                    'connector' => Evaluator::AND_CONNECTOR,
                    'negate' => false
                ],
                [
                    'type' => 'condition',
                    'condition' => 'contains',
                    'args' => [
                        '@@resolution',
                        '2160'
                    ],
                    'connector' => Evaluator::AND_CONNECTOR,
                    'negate' => false
                ]
            ]);

        $this->assertIsBool($this->evaluator->evaluate());
    }

    public function testEvaluatorReturnsFalseWithNoConditionsAdded()
    {
        $this->assertFalse($this->evaluator->evaluate());
    }

    public function testConditionCanBeDefinedFromClass()
    {
        $evaluator = new Evaluator();

        $evaluator->defineFromClass(\Tests\Conditions\Magic::class);

        $this->assertArrayHasKey(\Tests\Conditions\Magic::getRegisterName(), $evaluator->getDefinedConditions());
    }


}
