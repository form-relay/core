<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class RegexpEvaluation extends Evaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return preg_match('/' . $this->config . '/', $fieldValue);
    }
}
