<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\KeyEvaluation;

class KeyEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addEvaluation(KeyEvaluation::class);
    }

    /** @test */
    public function keyWithFieldEvalTrue()
    {
        $this->setupDummyData(2);
        $config = [
            'field1' => [
                'key' => 'field1',
            ],
        ];
        $result = $this->runEvaluationTest($config);
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
        $result = $this->runEvaluationTest($config);
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
        $result = $this->runEvaluationTest($config);
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
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
}
