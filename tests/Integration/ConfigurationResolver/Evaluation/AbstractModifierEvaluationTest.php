<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
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
        $this->data['field1'] = $value;
        $config = [
            'field1' => [
                static::KEYWORD => $modifiedValue,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /**
     * @param $value
     * @param $modifiedValue
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue($value, $modifiedValue)
    {
        $this->addContentResolver(MultiValueContentResolver::class);
        $this->data['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                static::KEYWORD => [
                    'in' => $modifiedValue,
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }
}
