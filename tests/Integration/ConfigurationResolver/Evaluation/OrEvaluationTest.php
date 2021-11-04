<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\OrEvaluation;

class OrEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(OrEvaluation::class);
        $this->setupDummyData();
    }

    /** @test */
    public function allTrue()
    {
        $config = [
            'or' => [
                'field1' => 'value1',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allFalse()
    {
        $config = [
            'or' => [
                'field1' => 'value4',
                'field2' => 'value5',
                'field3' => 'value6',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function someTrue()
    {
        $config = [
            'or' => [
                'field1' => 'value4',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function complexNestedConditionEvalTrue()
    {
        $this->registry->registerEvaluation(AndEvaluation::class);
        $config = [
            'or' => [
                1 => [
                    'and' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                ],
                2 => [
                    'and' => [
                        'field2' => 'value4',
                        'field3' => 'value5',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function complexNestedConditionEvalFalse()
    {
        $this->registry->registerEvaluation(AndEvaluation::class);
        $config = [
            'or' => [
                1 => [
                    'and' => [
                        'field1' => 'value1',
                        'field2' => 'value4',
                    ],
                ],
                2 => [
                    'and' => [
                        'field2' => 'value2',
                        'field3' => 'value5',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
