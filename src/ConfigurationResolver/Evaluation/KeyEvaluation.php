<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class KeyEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        $this->context['useKey'] = true;
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->resolveKeyword('general', $this->config);
        return $evaluation->eval($keysEvaluated);
    }
}
