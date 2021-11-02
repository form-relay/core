<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\ExistsEvaluation;

class ExistsEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(ExistsEvaluation::class);
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
        $result = $this->runEvaluationTest($config);
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
        $result = $this->runEvaluationTest($config);
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
        $result = $this->runEvaluationTest($config);
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
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
}