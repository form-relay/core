<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\ValueEvaluation;

/**
 * @covers ValueEvaluation
 */
class ValueEvaluationTest extends SelfEvaluationTest
{
    const KEY_SELF = 'value';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(ValueEvaluation::class);
    }
}
