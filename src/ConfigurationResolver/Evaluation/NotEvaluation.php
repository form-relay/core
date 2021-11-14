<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class NotEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->configuration);
        return !$evaluation->eval($keysEvaluated);
    }
}
