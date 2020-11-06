<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class InEvaluation extends Evaluation
{
    protected function convertScalarConfigToArray()
    {
        return true;
    }

    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return in_array($fieldValue, $this->config);
    }
}
