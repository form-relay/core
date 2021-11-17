<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers RegexpEvaluation
 */
class RegexpEvaluationTest extends AbstractEvaluationTest
{
    public function regexpProvider(): array
    {
        return [
            // value, regexp, match
            ['value1', 'value1',      true],
            ['value1', 'value2',      false],
            ['value1', 'alu',         true],
            ['value1', '^alu',        false],
            ['value1', '^val',        true],
            ['value1', 'val$',        false],
            ['value1', 'ue1$',        true],
            ['value1', '^[a-z0-9]+$', true],
        ];
    }

    public function regexpMultiValueProvider(): array
    {
        return [
            // value, regexp, match
            [['value1'],          'value1',     true],
            [['value1'],          'value[123]', true],
            [['value1','value2'], 'value1',     true],
            [['value1','value2'], 'abc',        false],
        ];
    }

    /**
     * @param $value
     * @param $regexp
     * @param $match
     * @dataProvider regexpProvider
     * @test
     */
    public function regexp($value, $regexp, $match)
    {
        $this->submissionData['field1'] = $value;
        $config = [
            'field1' => [
                'regexp' => $regexp,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($match) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @param $value
     * @param $regexp
     * @param $match
     * @dataProvider regexpMultiValueProvider
     * @test
     */
    public function regexpMultiValue($value, $regexp, $match)
    {
        $this->submissionData['field1'] = new MultiValueField($value);
        $config = [
            'field1' => [
                'regexp' => $regexp,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        if ($match) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
