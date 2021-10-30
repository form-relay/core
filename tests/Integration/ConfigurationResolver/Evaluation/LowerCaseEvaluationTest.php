<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\LowerCaseContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\LowerCaseEvaluation;

class LowerCaseEvaluationTest extends AbstractModifierEvaluationTest
{
    const KEYWORD = 'lowerCase';

    protected function setUp(): void
    {
        parent::setUp();
        $this->addEvaluation(LowerCaseEvaluation::class);
        $this->addContentResolver(LowerCaseContentResolver::class);
    }

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
            [[], []],
            [['Value1', 'VALUE2', 'value3'], ['value1', 'value2', 'value3']],
        ];
    }
}
