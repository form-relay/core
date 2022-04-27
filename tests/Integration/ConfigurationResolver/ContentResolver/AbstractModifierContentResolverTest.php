<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

abstract class AbstractModifierContentResolverTest extends AbstractContentResolverTest
{
    const KEYWORD = '';

    abstract public function modifyProvider(): array;

    abstract public function modifyMultiValueProvider(): array;

    public function disabledConfigurationProvider(): array
    {
        return [
            [false],
            [[]],
            [new MultiValueField([])],
            [['if' => [
                'field1' => 'value999', // => false
                'then' => true,
                'else' => false,
            ]]],
            [['if' => [
                'field1' => 'value999', // => false
                'then' => true,
            ]]],
        ];
    }

    /**
     * A standard value that the modifier can run on.
     * Used for testing a disabled modifier where the value should never change.
     * @return mixed
     */
    protected function getStandardValue()
    {
        return 'standardValue';
    }

    /**
     * @param $disabledConfig
     * @dataProvider disabledConfigurationProvider
     * @test
     */
    public function modifierDisabledDoesNotChangeValue($disabledConfig)
    {
        $this->submissionData['field1'] = 'value1';
        $value = $this->getStandardValue();
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
            static::KEYWORD => $disabledConfig,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals($value, $result);
    }

    /**
     * @param $value
     * @param $expected
     * @param $modifierConfig
     * @dataProvider modifyProvider
     * @test
     */
    public function modify($value, $expected, $modifierConfig = true)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
            static::KEYWORD => $modifierConfig,
        ];
        $result = $this->runResolverProcess($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $value
     * @param $expected
     * @param $modifierConfig
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    public function modifyMultiValue($value, $expected, $modifierConfig = true)
    {
        $config = [
            'multiValue' => $value,
            static::KEYWORD => $modifierConfig,
        ];
        $result = $this->runResolverProcess($config);
        if (empty($expected)) {
            $this->assertMultiValueEmpty($result);
        } else {
            $this->assertMultiValueEquals($expected, $result);
        }
    }

    /**
     * @param $value
     * @param $expected
     * @param $modifierConfig
     * @dataProvider modifyMultiValueProvider
     * @test
     */
    protected function modifyNestedMultiValue($value, $expected, $modifierConfig = true)
    {
        $config = [
            'multiValue' => [
                ['multiValue' => $value],
            ],
            static::KEYWORD => $modifierConfig,
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverProcess($config);
        $this->assertMultiValue($result);
        $result = $result->toArray()[0];
        $this->assertMultiValueEquals($expected, $result);
    }
}
