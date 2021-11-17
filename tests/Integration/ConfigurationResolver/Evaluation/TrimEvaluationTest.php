<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\TrimEvaluation;

/**
 * @covers TrimEvaluation
 */
class TrimEvaluationTest extends AbstractModifierEvaluationTest
{
    const KEYWORD = 'trim';

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
            [[' value3 ', 'value4'], ['value3', 'value4']],
        ];
    }
}
