<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class EqualsEvaluation extends AbstractComparisonEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return $this->compare($fieldValue, $this->resolveContent($this->configuration));
    }
}
