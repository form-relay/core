<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\IsFalseEvaluation;

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
            // TODO multiValue fields with no items will cause disjunctive evaluations to be always false
            //      and conjunctive evaluations to be always true
            //      we may need an additional check on the whole field (in eval()), not just on evalValue()
            // [[],           true,  /* => */ true],
            // [[],           false, /* => */ false],

            [['value1'], true,  /* => */ false],
            [['value1'], false, /* => */ true],

            [['', 'value2'], true,  /* => */ true],
            [['', 'value2'], false, /* => */ false],
            [['value1', ''], true,  /* => */ true],
            [['value1', ''], false, /* => */ false],
        ];
    }
}
