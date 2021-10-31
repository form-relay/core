<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

class EmptyEvaluationTest extends AbstractIsEvaluationTest
{
    const KEYWORD = 'empty';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(EmptyEvaluation::class);
    }

    public function isProvider(): array
    {
        return [
            // value, is, => expected
            [null,     true,  /* => */ true],
            [null,     false, /* => */ false],
            ['',       true,  /* => */ true],
            ['',       false, /* => */ false],
            ['0',      true,  /* => */ false],
            ['0',      false, /* => */ true],
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
            // [new MultiValueField(),           true,  /* => */ true],
            // [new MultiValueField(),           false, /* => */ false],

            [new MultiValueField(['value1']), true,  /* => */ false],
            [new MultiValueField(['value1']), false, /* => */ true],

            [new MultiValueField(['', 'value2']), true,  /* => */ true],
            [new MultiValueField(['', 'value2']), false, /* => */ false],
            [new MultiValueField(['value1', '']), true,  /* => */ true],
            [new MultiValueField(['value1', '']), false, /* => */ false],
        ];
    }
}
