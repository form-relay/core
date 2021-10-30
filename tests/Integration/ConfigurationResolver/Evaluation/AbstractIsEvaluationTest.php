<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

abstract class AbstractIsEvaluationTest extends AbstractEvaluationTest
{
    const KEYWORD = '';

    abstract public function isProvider(): array;
    abstract public function isMultiValueProvider(): array;

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
            $this->data['field1'] = $value;
        }
        $config = [
            'field1' => [
                static::KEYWORD => $is,
            ],
        ];
        $result = $this->runEvaluationTest($config);
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
        $this->data['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                static::KEYWORD => $is,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
