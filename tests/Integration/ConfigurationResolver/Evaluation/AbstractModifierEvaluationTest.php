<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\InEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

abstract class AbstractModifierEvaluationTest extends AbstractEvaluationTest
{
    const KEYWORD = '';

    abstract public function modifyProvider(): array;
    abstract public function modifyMultiValueProvider(): array;

    /**
     * @param $value
     * @param $modifiedValue
     * @dataProvider modifyProvider
     * @test
     */
    public function modify($value, $modifiedValue)
    {
        $this->submissionData['field1'] = $value;
        $config = [
            'field1' => [
                static::KEYWORD => $modifiedValue,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    // TODO the equals comparison should check if it has to compare two multi values
    //      and if it does, it should check if the values are equal
    //      once this is implemented, we can here use the "equals" evaluation instead of "in"
    /**
     * @param $value
     * @param $modifiedValue
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue($value, $modifiedValue)
    {
        $this->registry->registerEvaluation(InEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                static::KEYWORD => [
                    'in' => $modifiedValue,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function modifyEmptyMultiValue()
    {
        $this->registry->registerEvaluation(EmptyEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField();
        $config = [
            'field1' => [
                static::KEYWORD => [
                    'empty' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }
}
