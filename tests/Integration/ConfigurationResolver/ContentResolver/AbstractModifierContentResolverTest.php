<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

abstract class AbstractModifierContentResolverTest extends AbstractContentResolverTest
{
    const KEYWORD = '';

    abstract public function modifyProvider(): array;

    abstract public function modifyMultiValueProvider(): array;

    protected function runModify($value, $expected, $enabled)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
            static::KEYWORD => $enabled,
        ];
        $result = $this->runResolverTest($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider modifyProvider
     * @test
     */
    public function modify($value, $expected)
    {
        $this->runModify($value, $expected, true);
        $this->runModify($value, $value, false);
    }

    protected function runModifyMultiValue($value, $expected, $enabled)
    {
        $config = [
            'multiValue' => $value,
            static::KEYWORD => $enabled,
        ];
        $result = $this->runResolverTest($config);
        if (empty($expected)) {
            $this->assertMultiValueEmpty($result);
        } else {
            $this->assertMultiValueEquals($expected, $result);
        }
    }


    // TODO multi values are not taken into account yet
    /**
     * @param $value
     * @param $expected
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue($value, $expected)
    {
        $this->markTestSkipped();
        $this->runModifyMultiValue($value, $expected, true);
        $this->runModifyMultiValue($value, $value, false);
    }

    protected function runModifyNestedMultiValue($value, $expected, $enabled)
    {
        $config = [
            'multiValue' => [
                'multiValue' => $value,
            ],
            static::KEYWORD => $enabled,
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertMultiValue($result);
        $result = $result->toArray()[0];
        $this->assertMultiValueEquals($expected, $result);
    }

    // TODO multi values are not taken into account yet
    /**
     * @param $value
     * @param $expected
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyNestedMultiValue($value, $expected)
    {
        $this->markTestSkipped();
        $this->runModifyNestedMultiValue($value, $expected, true);
        $this->runModifyNestedMultiValue($value, $value, false);
    }
}
