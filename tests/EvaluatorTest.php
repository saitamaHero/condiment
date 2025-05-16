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
                    ['equals', [1, 1], Evaluator::AND_CONNECTOR, false],
                    ['contains', ["Hello", "ll"], Evaluator::AND_CONNECTOR, false],
                ],
                true
            ],
            'Conditions are grouped correctly' => [
                [
                    ['equals', [1, 2], Evaluator::AND_CONNECTOR, false],
                    ['contains', ["Hello", "b"], Evaluator::AND_CONNECTOR, false],
                    ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false],
                ],
                true
            ],
            'Groups are created manually' => [
                [
                    ['equals', [1, 2], Evaluator::AND_CONNECTOR, false],
                    'and' => [
                        ['contains', ["Hello", "b"], Evaluator::AND_CONNECTOR, false],
                        ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false]
                    ],
                ],
                false
            ],
            'Nested groups can be added manually' => [
                [
                    ['equals', [1, 2], Evaluator::AND_CONNECTOR, false],
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
                    ['equals', [1, 2], Evaluator::AND_CONNECTOR, false],
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
            'Deeply nested conditions' => [
                [
                    ['equals', [1, 1], Evaluator::AND_CONNECTOR, false],
                    'and' => [
                        ['contains', ["Hello", "H"], Evaluator::AND_CONNECTOR, false],
                        'or' => [
                            ['match', ["condition", "tio(n|nal)"], Evaluator::OR_CONNECTOR, false],
                            'and' => [
                                ['endsWith', ["PHP", "HP"], Evaluator::AND_CONNECTOR, false],
                                'or' => [
                                    ['startsWith', ["PHP", "PH"], Evaluator::OR_CONNECTOR, false],
                                    'and' => [
                                        ['gt', [10, 5], Evaluator::AND_CONNECTOR, false],
                                        ['lt', [5, 10], Evaluator::AND_CONNECTOR, false],
                                    ],
                                ],
                            ],
                        ],
                    ],
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
                    'gt',
                    [
                        '@@ratings.totalReviews',
                        100
                    ]
                ],
                [
                    'contains',
                    [
                        '@@resolution',
                        '2160'
                    ]
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
                    'gt',
                    [
                        '@@ratings.missingProperty',
                        100
                    ]
                ],
                [
                    'contains',
                    [
                        '@@resolution',
                        '2160'
                    ]
                ]
            ]);

        $this->assertIsBool($this->evaluator->evaluate());
    }
}
