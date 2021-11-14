<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\AllEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\NotEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers AnyEvaluation
 */
class AllEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(AllEvaluation::class);
        $this->registry->registerEvaluation(RegexpEvaluation::class);
    }

    /** @test */
    public function allOfMultiValueRegexpEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'field1' => [
                'all' => [
                    'regexp' => 'value[0-9]',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfMultiValueEqualsEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'field1' => [
                'all' => [
                    'regexp' => 'value[12]'
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfEmptyMultiValueEqualsEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField([]);
        $config = [
            'field1' => [
                'all' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfNonExistentFieldEqualsEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'field2' => [
                'all' => [
                    'regexp' => 'value[0-9]',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfScalarValueEqualsEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'all' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfScalarValueEqualsEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'all' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfMultiValueEqualsNotMatchesNoneEvalTrue()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field1' => [
                'all' => [
                    'not' => 42,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfMultiValueEqualsNotMatchesOneEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField([5, 7, 13]);
        $config = [
            'field1' => [
                'all' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfMultiValueEqualsNotMatchesAllEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField([7, 7, 7]);
        $config = [
            'field1' => [
                'all' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
