<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\RequiredEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

class RequiredEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addEvaluation(RequiredEvaluation::class);
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
        $this->data['notEmptyField'] = 'value1';
        $this->data['notEmptyField2'] = 'value2';
        $this->data['emptyField'] = '';
        $this->data['emptyField2'] = '';
        $config = [
            'required' => $required,
        ];
        $result = $this->runEvaluationTest($config);
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
        $this->data['notEmptyField'] = new MultiValueField(['value1']);
        $this->data['notEmptyField2'] = new MultiValueField(['value2']);
        $this->data['emptyField'] = new MultiValueField();
        $this->data['emptyField2'] = new MultiValueField();
        $config = [
            'required' => $required,
        ];
        $result = $this->runEvaluationTest($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
