<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class InEvaluation extends AbstractComparisonEvaluation
{
    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_RESOLVE_CONTENT_THEN_CAST_TO_ARRAY;
    }

    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return $this->isInList($fieldValue, $this->configuration);
    }
}
