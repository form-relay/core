<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\AllEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

abstract class AbstractIsEvaluationTest extends AbstractEvaluationTest
{
    const KEYWORD = '';

    abstract public function isProvider(): array;
    abstract public function isMultiValueProvider(): array;
    abstract public function anyIsMultiValueProvider(): array;
    abstract public function allIsMultiValueProvider(): array;

    /**
     * @param $value
     * @param $is
     * @param $expected
     * @dataProvider isProvider
     * @test
     */
    public function is($value, $is, $expected)
    {
        if ($value !== null) {
            $this->submissionData['field1'] = $value;
        }
        $config = [
            'field1' => [
                static::KEYWORD => $is,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $value
     * @param $is
     * @param $expected
     * @dataProvider isMultiValueProvider
     * @test
     */
    public function isMultiValue($value, $is, $expected)
    {
        $this->submissionData['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                static::KEYWORD => $is,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $value
     * @param $is
     * @param $expected
     * @dataProvider anyIsMultiValueProvider
     * @test
     */
    public function anyIsMultiValue($value, $is, $expected)
    {
        $this->registry->registerEvaluation(AnyEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                'any' => [
                    static::KEYWORD => $is,
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $value
     * @param $is
     * @param $expected
     * @dataProvider allIsMultiValueProvider
     * @test
     */
    public function allIsMultiValue($value, $is, $expected)
    {
        $this->registry->registerEvaluation(AllEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                'all' => [
                    static::KEYWORD => $is,
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
