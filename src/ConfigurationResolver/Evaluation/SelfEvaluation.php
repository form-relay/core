<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class SelfEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        if (!$this->getKeyFromContext()) {
            return (bool)$this->configuration;
        }

        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('equals', $this->configuration);
        return $evaluation->eval($keysEvaluated);
    }
}
