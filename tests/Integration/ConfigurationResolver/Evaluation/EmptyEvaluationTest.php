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
        ];
    }

    public function anyIsMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [new MultiValueField([]),                   true,  /* => */ false],
            [new MultiValueField([]),                   false, /* => */ false],
            [new MultiValueField(['value1']),           true,  /* => */ false],
            [new MultiValueField(['value1']),           false, /* => */ true],
            [new MultiValueField(['value1', 'value2']), true,  /* => */ false],
            [new MultiValueField(['value1', 'value2']), false, /* => */ true],
            [new MultiValueField(['', 'value2']),       true,  /* => */ true],
            [new MultiValueField(['', 'value2']),       false, /* => */ true],
            [new MultiValueField(['value1', '']),       true,  /* => */ true],
            [new MultiValueField(['value1', '']),       false, /* => */ true],
            [new MultiValueField(['']),                 true,  /* => */ true],
            [new MultiValueField(['']),                 false, /* => */ false],
            [new MultiValueField(['', '']),             true,  /* => */ true],
            [new MultiValueField(['', '']),             false, /* => */ false],
        ];
    }

    public function allIsMultiValueProvider(): array
    {
        return [
            [new MultiValueField([]),                   true,  /* => */ true],
            [new MultiValueField([]),                   false, /* => */ true],
            [new MultiValueField(['value1']),           true,  /* => */ false],
            [new MultiValueField(['value1']),           false, /* => */ true],
            [new MultiValueField(['value1', 'value2']), true,  /* => */ false],
            [new MultiValueField(['value1', 'value2']), false, /* => */ true],
            [new MultiValueField(['', 'value2']),       true,  /* => */ false],
            [new MultiValueField(['', 'value2']),       false, /* => */ false],
            [new MultiValueField(['value1', '']),       true,  /* => */ false],
            [new MultiValueField(['value1', '']),       false, /* => */ false],
            [new MultiValueField(['']),                 true,  /* => */ true],
            [new MultiValueField(['']),                 false, /* => */ false],
            [new MultiValueField(['', '']),             true,  /* => */ true],
            [new MultiValueField(['', '']),             false, /* => */ false],
        ];
    }
}
