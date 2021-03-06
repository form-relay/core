<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class OrEvaluation extends AndEvaluation
{
    protected function initialValue(): bool
    {
        return false;
    }

    protected function calculate(bool $result, EvaluationInterface $evaluation, array $keysEvaluated): bool
    {
        return $result || $evaluation->eval($keysEvaluated);
    }
}
