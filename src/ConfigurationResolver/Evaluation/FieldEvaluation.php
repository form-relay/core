<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class FieldEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        if (!is_array($this->configuration) || count($this->configuration) !== 1) {
                return false;
        }

        $key = array_keys($this->configuration)[0];
        $this->addKeyToContext($key);
        /** @var EvaluationInterface $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->configuration[$key]);
        return $evaluation->eval($keysEvaluated);
    }
}
