<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class EmptyEvaluation extends Evaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return !!$fieldValue;
    }

    public function eval(array $keysEvaluated = []): bool
    {
        // not empty
        $result = parent::eval($keysEvaluated);

        // empty
        if ($this->config) {
            $result = !$result;
        }

        return $result;
    }
}
