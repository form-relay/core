<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers AnyEvaluation
 */
class AnyEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function anyOfMultiValueEqualsEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => 7,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => 42,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfEmptyMultiValueEqualsEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField([]);
        $config = [
            'field1' => [
                'any' => 7,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfNonExistentFieldEqualsEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field2' => [
                'any' => 7,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfScalarValueEqualsEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'any' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfScalarValueEqualsEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'any' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsNotMatchesNoneEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => [
                    'not' => 42,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsNotMatchesOneEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsNotMatchesAllEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField([7, 7, 7]);
        $config = [
            'field1' => [
                'any' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
