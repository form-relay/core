<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class EqualsEvaluation extends Evaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return $fieldValue === $this->resolveContent($this->configuration);
    }
}
