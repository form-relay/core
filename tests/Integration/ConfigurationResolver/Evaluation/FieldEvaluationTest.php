<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\FieldEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\NotEvaluation;

/**
 * @covers FieldEvaluation
 */
class FieldEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(FieldEvaluation::class);
    }

    /** @test */
    public function fieldEqualsEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldEqualsEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => 'value4',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldDoesNotExistEqualsEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldEqualsNotEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'not' => 'value1',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldEqualsNotEvalTrue()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'not' => 'value2',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldNotEqualsEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'not' => [
                'field' => [
                    'field1' => 'value1',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldNotEqualsEvalTrue()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'not' => [
                'field' => [
                    'field1' => 'value2',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordEqualsEvalTrue()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['not'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordEqualsEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['not'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldKeywordDoesNotExistEqualsEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    public function newFieldOverwritesCurrentFieldProvider(): array
    {
        return [
            [false, false],
            [false, true],
            [true,  false],
            [true,  true],
        ];
    }

    /**
     * @param bool $fieldFieldImplicit
     * @param bool $secondFieldImplicit
     * @dataProvider newFieldOverwritesCurrentFieldProvider
     * @test
     */
    public function newFieldOverwritesCurrentFieldEvalTrue(bool $fieldFieldImplicit, bool $secondFieldImplicit)
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'field' => [
                        'field1' => 'value1',
                    ],
                ],
            ],
        ];
        if ($secondFieldImplicit) {
            $config['field']['field2'] = $config['field']['field2']['field'];
        }
        if ($fieldFieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $firstFieldImplicit
     * @param bool $secondFieldImplicit
     * @dataProvider newFieldOverwritesCurrentFieldProvider
     * @test
     */
    public function newFieldOverwritesCurrentFieldEvalFalse(bool $firstFieldImplicit, bool $secondFieldImplicit)
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'field' => [
                        'field1' => 'value2',
                    ],
                ],
            ],
        ];
        if ($secondFieldImplicit) {
            $config['field']['field2'] = $config['field']['field2']['field'];
        }
        if ($firstFieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
