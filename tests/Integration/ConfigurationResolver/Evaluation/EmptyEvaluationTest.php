<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers EmptyEvaluation
 */
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
            [new MultiValueField([]),             true,  /* => */ true],
            [new MultiValueField([]),             false, /* => */ false],
            [new MultiValueField(['value1']),     true,  /* => */ false],
            [new MultiValueField(['value1']),     false, /* => */ true],
            [new MultiValueField(['', 'value2']), true,  /* => */ false],
            [new MultiValueField(['', 'value2']), false, /* => */ true],
            [new MultiValueField(['value1', '']), true,  /* => */ false],
            [new MultiValueField(['value1', '']), false, /* => */ true],
            [new MultiValueField(['']),           true,  /* => */ false],
            [new MultiValueField(['']),           false, /* => */ true],
            [new MultiValueField(['', '']),       true,  /* => */ false],
            [new MultiValueField(['', '']),       false, /* => */ true],

            // TODO as soon as the evaluations "any" and "all" are imlpemented
            //      that needs additional testing
            //      because then we do want to test the items inside the multi-value field
            //      instead of the field as a whole
            //      this decision has to be made for the isTrue and isFalse evaluations as well
            //      even though it is not as clear for them as it is for the empty evaluation
        ];
    }
}
