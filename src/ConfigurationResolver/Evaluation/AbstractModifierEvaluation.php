<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

abstract class AbstractModifierEvaluation extends Evaluation
{
    protected function getModifierName()
    {
        return $this->getKeyword();
    }

    protected function getModifierConfiguration()
    {
        return true;
    }

    protected function getModifierObject(): array
    {
        return [$this->getModifierName() => $this->getModifierConfiguration()];
    }

    public function eval(array $keysEvaluated = []): bool
    {
        $this->addModifierToContext($this->getModifierObject());
        if (is_array($this->configuration)) {
            /** @var EvaluationInterface $evaluation */
            $evaluation = $this->resolveKeyword('general', $this->configuration);
        } else {
            /** @var EvaluationInterface $evaluation */
            $evaluation = $this->resolveKeyword('equals', $this->configuration);
        }
        return $evaluation->eval($keysEvaluated);
    }
}
