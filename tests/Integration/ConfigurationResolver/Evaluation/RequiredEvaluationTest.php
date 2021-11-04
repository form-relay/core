<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers RequiredEvaluation
 */
class RequiredEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(RequiredEvaluation::class);
    }

    public function requiredProvider(): array
    {
        return [
            ['notEmptyField',                             true],
            ['emptyField',                                false],
            ['notExistingField',                          false],
            ['notEmptyField,notEmptyField2',              true],
            ['emptyField,notEmptyField',                  false],
            ['emptyField,notExistingField',               false],
            ['notEmptyField,notExistingField',            false],
            ['emptyField,notEmptyField,notExistingField', false],
        ];
    }

    /**
     * @param $required
     * @param $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function required($required, $expected)
    {
        $this->submissionData['notEmptyField'] = 'value1';
        $this->submissionData['notEmptyField2'] = 'value2';
        $this->submissionData['emptyField'] = '';
        $this->submissionData['emptyField2'] = '';
        $config = [
            'required' => $required,
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $required
     * @param $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredMultiValue($required, $expected)
    {
        $this->submissionData['notEmptyField'] = new MultiValueField(['value1']);
        $this->submissionData['notEmptyField2'] = new MultiValueField(['value2']);
        $this->submissionData['emptyField'] = new MultiValueField();
        $this->submissionData['emptyField2'] = new MultiValueField();
        $config = [
            'required' => $required,
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
