<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\ValueEvaluation;

class ValueEvaluationTest extends SelfEvaluationTest
{
    const KEY_SELF = 'value';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(ValueEvaluation::class);
    }
}
