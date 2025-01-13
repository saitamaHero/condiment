<?php

use Condiment\Evaluables\Evaluator;
use PHPUnit\Framework\TestCase;

final class EvaluatorTest extends TestCase
{
 
    public function test_evaluator_can_be_instantiated_it()
    {
        $evaluator = new Evaluator();

        $this->assertInstanceOf(Evaluator::class, $evaluator);
    }
    
    public function test_evaluator_can_define_external_conditions()
    {
        $evaluator = new Evaluator();

        $evaluator->defineCondition('custom', Condiment\Evaluables\Conditions\Definitions\Equals::class);

        $this->assertArrayHasKey('custom', $evaluator->conditionDefinitions);
    }

    public function test_external_conditions_can_be_called_with_magic_methods()
    {
        $evaluator = new Evaluator();

        $evaluator->defineCondition('custom', Condiment\Evaluables\Conditions\Definitions\Equals::class);

        $this->assertArrayHasKey('custom', $evaluator->conditionDefinitions);
    }
}