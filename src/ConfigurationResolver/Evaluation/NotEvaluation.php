<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class NotEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword(
            is_array($this->config) ? 'general' : 'equals',
            $this->config,
            $this->context
        );
        return !$evaluation->eval($keysEvaluated);
    }
}
