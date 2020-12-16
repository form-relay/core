<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class ProcessedEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // processed
        $result = $this->context['tracker']->hasBeenProcessed($this->context['key']);

        // not processed
        if (!$this->config) {
            $result = !$result;
        }

        return $result;
    }

}
