<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IsFalseEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IsTrueEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers IsTrueEvaluation
 */
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
            [[],             true,  /* => */ false],
            [[],             false, /* => */ true],
            [[''],           true,  /* => */ true],
            [[''],           false, /* => */ false],
            [['value1'],     true,  /* => */ true],
            [['value1'],     false, /* => */ false],
            [['', 'value2'], true,  /* => */ true],
            [['', 'value2'], false, /* => */ false],
            [['value1', ''], true,  /* => */ true],
            [['value1', ''], false, /* => */ false],
        ];
    }

    public function anyIsMultiValueProvider(): array
    {
        return [
            // value, is, => expected
            [[],                   true,  /* => */ false],
            [[],                   false, /* => */ false],
            [[''],                 true,  /* => */ false],
            [[''],                 false, /* => */ true],
            [['value1'],           true,  /* => */ true],
            [['value1'],           false, /* => */ false],
            [['value1', 'value2'], true,  /* => */ true],
            [['value1', 'value2'], false, /* => */ false],
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
            [[''],                 true,  /* => */ false],
            [[''],                 false, /* => */ true],
            [['value1'],           true,  /* => */ true],
            [['value1'],           false, /* => */ false],
            [['value1', 'value2'], true,  /* => */ true],
            [['value1', 'value2'], false, /* => */ false],
            [['', 'value2'],       true,  /* => */ false],
            [['', 'value2'],       false, /* => */ false],
            [['value1', ''],       true,  /* => */ false],
            [['value1', ''],       false, /* => */ false],
        ];
    }
}
