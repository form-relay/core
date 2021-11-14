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
     * @param mixed $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider isProvider
     * @test
     */
    public function is($value, bool $is, bool $expected)
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
     * @param array $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider isMultiValueProvider
     * @test
     */
    public function isMultiValue(array $value, bool $is, bool $expected)
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
     * @param array $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider anyIsMultiValueProvider
     * @test
     */
    public function anyIsMultiValue(array $value, bool $is, bool $expected)
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
     * @param array $value
     * @param bool $is
     * @param bool $expected
     * @dataProvider allIsMultiValueProvider
     * @test
     */
    public function allIsMultiValue(array $value, bool $is, bool $expected)
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
