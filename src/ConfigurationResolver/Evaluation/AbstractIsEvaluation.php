<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

abstract class AbstractIsEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // positive evaluation
        $result = parent::eval($keysEvaluated);

        // negative evaluation
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
