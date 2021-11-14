<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class OrEvaluation extends AbstractClauseEvaluation
{
    protected function initialValue(): bool
    {
        return false;
    }

    protected function calculate(bool $result, EvaluationInterface $evaluation, array $keysEvaluated): bool
    {
        return $evaluation->eval($keysEvaluated) || $result;
    }
}
