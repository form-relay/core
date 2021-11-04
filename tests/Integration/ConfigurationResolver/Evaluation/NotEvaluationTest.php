<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\NotEvaluation;

class NotEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->setupDummyData();
    }

    /** @test */
    public function notUnaryEvalTrue()
    {
        $config = [
            'not' => '0',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    // TODO currently scalar values trigger the equals evaluation
    //      they should just trigger a standard general evaluation instead
    /** @test */
    public function notUnaryEvalFalse()
    {
        $this->markTestSkipped();
        $config = [
            'not' => '1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notFieldEqualsEvalTrue()
    {
        $config = [
            'not' => [
                'field1' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notFieldEqualsEvalFalse()
    {
        $config = [
            'not' => [
                'field1' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldNotEqualsEvalTrue()
    {
        $config = [
            'field1' => [
                'not' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldNotEqualsEvalFalse()
    {
        $config = [
            'field1' => [
                'not' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
