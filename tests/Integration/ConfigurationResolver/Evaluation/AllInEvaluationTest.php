<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AllInEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers AllInEvaluation
 */
class AllInEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(AllInEvaluation::class);
    }

    /** @test */
    public function allInNotExistingFieldReturnsFalse()
    {
        $config = [
            'field1' => [
                'allIn' => '4,5,6,7,8,16,17,18',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allIn()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => '4,5,6,7,8,16,17,18',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allInList()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => [
                    'list' => [4, 5, 6, 7, 8, 16, 17, 18],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notAllIn()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => '4,5,6,7,8,16,18',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notAllInList()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => [
                    'list' => [4,5,6,7,8,16,18],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function noneIn()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => '4,6,8,16,18',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function noneInList()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => [
                    'list' => [4, 6, 8, 16, 18,],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
