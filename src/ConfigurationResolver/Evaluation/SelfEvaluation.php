<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class SelfEvaluation extends EqualsEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        if (!$this->getKeyFromContext()) {
            return (bool)$this->configuration;
        }
        return parent::evalValue($fieldValue, $keysEvaluated);
    }
}
