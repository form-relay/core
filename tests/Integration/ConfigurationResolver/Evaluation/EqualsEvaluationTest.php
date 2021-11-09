<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;

/**
 * @covers EqualsEvaluation
 */
class EqualsEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(EqualsEvaluation::class);
        $this->setupDummyData();
    }

    /** @test */
    public function equalsScalarEvalTrue()
    {
        $config = [
            'field1' => [
                'equals' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function equalsScalarEvalFalse()
    {
        $config = [
            'field1' => [
                'equals' => 'value4',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldEqualsScalarEvalTrue()
    {
        $config = [
            'field' => 'field1',
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldEqualsScalarEvalFalse()
    {
        $config = [
            'field' => 'field1',
            'equals' => 'value4',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function equalsComplexValueEvalTrue()
    {
        $config = [
            'field1' => [
                'equals' => [
                    1 => 'val',
                    2 => 'ue1',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function equalsComplexValueEvalFalse()
    {
        $config = [
            'field1' => [
                'equals' => [
                    1 => 'val',
                    2 => 'ue4',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
