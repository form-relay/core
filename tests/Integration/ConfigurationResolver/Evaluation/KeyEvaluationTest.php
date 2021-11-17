<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\KeyEvaluation;

/**
 * @covers KeyEvaluation
 */
class KeyEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function keyWithFieldEvalTrue()
    {
        $this->setupDummyData(2);
        $config = [
            'field1' => [
                'key' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function keyWithFieldEvalFalse()
    {
        $this->setupDummyData(2);
        $config = [
            'field1' => [
                'key' => 'field2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function keyWithoutFieldEvalTrue()
    {
        $config = [
            'field1' => [
                'key' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function keyWithoutFieldEvalFalse()
    {
        $config = [
            'field1' => [
                'key' => 'field2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
