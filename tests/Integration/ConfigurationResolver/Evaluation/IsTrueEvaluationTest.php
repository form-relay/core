<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IsFalseEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IsTrueEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

class IsTrueEvaluationTest extends IsFalseEvaluationTest
{
    const KEYWORD = 'isTrue';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(IsTrueEvaluation::class);
    }

    public function isProvider(): array
    {
        $provided = parent::isProvider();
        foreach ($provided as $index => $arguments) {
            $arguments[2] = !$arguments[2];
            $provided[$index] = $arguments;
        }
        return $provided;
    }

    public function isMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            // TODO multiValue fields with no items will cause disjunctive evaluations to be always false
            //      and conjunctive evaluations to be always true
            //      we may need an additional check on the whole field (in eval()), not just on evalValue()
            // [[],           true,  /* => */ false],
            // [[],           false, /* => */ true],

            [['value1'], true,  /* => */ true],
            [['value1'], false, /* => */ false],

            [['', 'value2'], true,  /* => */ true],
            [['', 'value2'], false, /* => */ false],
            [['value1', ''], true,  /* => */ true],
            [['value1', ''], false, /* => */ false],
        ];
    }
}
