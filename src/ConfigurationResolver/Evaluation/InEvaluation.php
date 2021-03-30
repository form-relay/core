<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class InEvaluation extends Evaluation
{
    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_EXPLODE;
    }

    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return in_array($fieldValue, $this->configuration);
    }
}
