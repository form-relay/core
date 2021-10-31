<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\TrimEvaluation;

class TrimEvaluationTest extends AbstractModifierEvaluationTest
{
    const KEYWORD = 'trim';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(TrimEvaluation::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
    }

    public function modifyProvider(): array
    {
        return [
            ["",            ""],
            [" ",           ""],
            ["\t",          ""],
            ["\n",          ""],
            [" value1 ",    "value1"],
            ["val ue1",     "val ue1"],
            [" val ue1 ",   "val ue1"],
            ["value1",      "value1"],
            ["\t value1\n", "value1"],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [[], []],
            [[' value3 ', 'value4'], ['value3', 'value4']],
        ];
    }
}
