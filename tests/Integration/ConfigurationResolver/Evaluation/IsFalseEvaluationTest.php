<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\IsFalseEvaluation;

/**
 * @covers IsFalseEvaluation
 */
class IsFalseEvaluationTest extends AbstractIsEvaluationTest
{
    const KEYWORD = 'isFalse';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(IsFalseEvaluation::class);
    }

    public function isProvider(): array
    {
        return [
            // value, is, => expected
            [null,     true,  /* => */ true],
            [null,     false, /* => */ false],
            ['',       true,  /* => */ true],
            ['',       false, /* => */ false],
            ['0',      true,  /* => */ true],
            ['0',      false, /* => */ false],
            ['1',      true,  /* => */ false],
            ['1',      false, /* => */ true],
            ['value1', true,  /* => */ false],
            ['value1', false, /* => */ true],
        ];
    }

    public function isMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],             true,  /* => */ true],
            [[],             false, /* => */ false],
            [[''],           true,  /* => */ false],
            [[''],           false, /* => */ true],
            [['value1'],     true,  /* => */ false],
            [['value1'],     false, /* => */ true],
            [['', 'value2'], true,  /* => */ false],
            [['', 'value2'], false, /* => */ true],
            [['value1', ''], true,  /* => */ false],
            [['value1', ''], false, /* => */ true],
        ];
    }

    public function anyIsMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],                   true,  /* => */ false],
            [[],                   false, /* => */ false],
            [[''],                 true,  /* => */ true],
            [[''],                 false, /* => */ false],
            [['value1'],           true,  /* => */ false],
            [['value1'],           false, /* => */ true],
            [['value1', 'value2'], true,  /* => */ false],
            [['value1', 'value2'], false, /* => */ true],
            [['', 'value2'],       true,  /* => */ true],
            [['', 'value2'],       false, /* => */ true],
            [['value1', ''],       true,  /* => */ true],
            [['value1', ''],       false, /* => */ true],
        ];
    }

    public function allIsMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],                   true,  /* => */ true],
            [[],                   false, /* => */ true],
            [[''],                 true,  /* => */ true],
            [[''],                 false, /* => */ false],
            [['value1'],           true,  /* => */ false],
            [['value1'],           false, /* => */ true],
            [['value1', 'value2'], true,  /* => */ false],
            [['value1', 'value2'], false, /* => */ true],
            [['', 'value2'],       true,  /* => */ false],
            [['', 'value2'],       false, /* => */ false],
            [['value1', ''],       true,  /* => */ false],
            [['value1', ''],       false, /* => */ false],
        ];
    }
}
