<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ContentResolver\UpperCaseContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\UpperCaseEvaluation;

/**
 * @covers UpperCaseEvaluation
 */
class UpperCaseEvaluationTest extends AbstractModifierEvaluationTest
{
    const KEYWORD = 'upperCase';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(UpperCaseEvaluation::class);
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
    }

    public function modifyProvider(): array
    {
        return [
            ['value1', 'VALUE1'],
            ['VALUE1', 'VALUE1'],
            ['1_2_3',  '1_2_3'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [['Value1', 'VALUE2', 'value3'], ['VALUE1', 'VALUE2', 'VALUE3']],
        ];
    }
}
