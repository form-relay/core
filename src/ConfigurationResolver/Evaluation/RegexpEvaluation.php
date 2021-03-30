<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class RegexpEvaluation extends Evaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        $regExp = $this->resolveContent($this->configuration);
        return preg_match('/' . $regExp . '/', $fieldValue);
    }
}
