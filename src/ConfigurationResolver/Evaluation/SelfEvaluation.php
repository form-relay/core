<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class SelfEvaluation extends EqualsEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        if (!$this->getKeyFromContext()) {
            // if no field name is given, treat this as unary operation
            return (bool)$this->configuration;
        }
        // otherwise it is a binary (equals) operation
        return parent::evalValue($fieldValue, $keysEvaluated);
    }
}
