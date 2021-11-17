<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers RequiredEvaluation
 */
class RequiredEvaluationTest extends AbstractEvaluationTest
{
    public function requiredProvider(): array
    {
        return [
            [['notEmptyField'],                                 true],
            [['emptyField'],                                    false],
            [['notExistingField'],                              false],
            [['notEmptyField','notEmptyField2'],                true],
            [['emptyField','notEmptyField'],                    false],
            [['emptyField','notExistingField'],                 false],
            [['notEmptyField','notExistingField'],              false],
            [['emptyField','notEmptyField','notExistingField'], false],
        ];
    }

    /**
     * @param array $required
     * @param bool $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function required(array $required, bool $expected)
    {
        $this->submissionData['notEmptyField'] = 'value1';
        $this->submissionData['notEmptyField2'] = 'value2';
        $this->submissionData['emptyField'] = '';
        $this->submissionData['emptyField2'] = '';
        $config = [
            'required' => implode(',', $required),
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $required
     * @param bool $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredList(array $required, bool $expected)
    {
        $this->submissionData['notEmptyField'] = 'value1';
        $this->submissionData['notEmptyField2'] = 'value2';
        $this->submissionData['emptyField'] = '';
        $this->submissionData['emptyField2'] = '';
        $config = [
            'required' => ['list' => $required],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $required
     * @param bool $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredComplexContentResolver(array $required, bool $expected)
    {
        $this->submissionData['notEmptyField'] = 'value1';
        $this->submissionData['notEmptyField2'] = 'value2';
        $this->submissionData['emptyField'] = '';
        $this->submissionData['emptyField2'] = '';
        $config = [
            'required' => ['glue' => ','],
        ];
        foreach ($required as $field) {
            $config['required'][] = $field;
        }
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $required
     * @param bool $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredMultiValue(array $required, bool $expected)
    {
        $this->submissionData['notEmptyField'] = new MultiValueField(['value1']);
        $this->submissionData['notEmptyField2'] = new MultiValueField(['value2']);
        $this->submissionData['emptyField'] = new MultiValueField();
        $this->submissionData['emptyField2'] = new MultiValueField();
        $config = [
            'required' => implode(',', $required),
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param array $required
     * @param bool $expected
     * @dataProvider requiredProvider
     * @test
     */
    public function requiredListMultiValue(array $required, bool $expected)
    {
        $this->submissionData['notEmptyField'] = new MultiValueField(['value1']);
        $this->submissionData['notEmptyField2'] = new MultiValueField(['value2']);
        $this->submissionData['emptyField'] = new MultiValueField();
        $this->submissionData['emptyField2'] = new MultiValueField();
        $config = [
            'required' => ['list' => $required],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
