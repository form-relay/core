<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\LowerCaseEvaluation;

/**
 * @covers LowerCaseEvaluation
 */
class LowerCaseEvaluationTest extends AbstractModifierEvaluationTest
{
    const KEYWORD = 'lowerCase';

    public function modifyProvider(): array
    {
        return [
            ['VALUE1', 'value1'],
            ['value1', 'value1'],
            ['1_2_3',  '1_2_3'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [['Value1', 'VALUE2', 'value3'], ['value1', 'value2', 'value3']],
        ];
    }
}
