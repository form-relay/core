<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class NotEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword(
            is_array($this->configuration) ? 'general' : 'equals',
            $this->configuration,
            $this->context
        );
        return !$evaluation->eval($keysEvaluated);
    }
}
