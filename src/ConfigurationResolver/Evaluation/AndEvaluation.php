<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class AndEvaluation extends AbstractClauseEvaluation
{
    protected function initialValue(): bool
    {
        return true;
    }

    protected function calculate(bool $result, EvaluationInterface $evaluation, array $keysEvaluated): bool
    {
        return $evaluation->eval($keysEvaluated) && $result;
    }
}
