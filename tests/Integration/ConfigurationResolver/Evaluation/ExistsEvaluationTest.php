<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\ExistsEvaluation;

/**
 * @covers ExistsEvaluation
 */
class ExistsEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDummyData();
    }

    /** @test */
    public function existsEvalTrue()
    {
        $config = [
            'field1' => [
                'exists' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function existsEvalFalse()
    {
        $config = [
            'field4' => [
                'exists' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function doesNotExistEvalTrue()
    {
        $config = [
            'field4' => [
                'exists' => false,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function doesNotExistEvalFalse()
    {
        $config = [
            'field1' => [
                'exists' => false,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
