<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\ProcessedEvaluation;

class ProcessedEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(ProcessedEvaluation::class);
        $this->setupDummyData();
    }

    /** @test */
    public function processedInEvaluationEvalTrue()
    {
        $config = [
            1 => [ 'field1' => 'value1', ],
            2 => [
                'field1' => [
                    'processed' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function processedInEvaluationEvalFalse()
    {
        $config = [
            1 => [ 'field1' => 'value1', ],
            2 => [
                'field1' => [
                    'processed' => false,
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notProcessedInEvaluationEvalTrue()
    {
        $config = [
            1 => [ 'field2' => 'value2', ],
            2 => [
                'field1' => [
                    'processed' => false,
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notProcessedInEvaluationEvalFalse()
    {
        $config = [
            1 => [ 'field2' => 'value2', ],
            2 => [
                'field1' => [
                    'processed' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function processedBeforeEvalTrue()
    {
        $this->fieldTracker->markAsProcessed('field1');
        $config = [
            'field1' => [
                'processed' => true,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function processedBeforeEvalFalse()
    {
        $this->fieldTracker->markAsProcessed('field1');
        $config = [
            'field1' => [
                'processed' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notProcessedBeforeEvalTrue()
    {
        $this->fieldTracker->markAsProcessed('field2');
        $config = [
            'field1' => [
                'processed' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notProcessedBeforeEvalFalse()
    {
        $this->fieldTracker->markAsProcessed('field2');
        $config = [
            'field1' => [
                'processed' => true,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
}
