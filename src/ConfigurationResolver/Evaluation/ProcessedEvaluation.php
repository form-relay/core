<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class ProcessedEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // processed
        $result = $this->context->getFieldTracker()->hasBeenProcessed($this->context['key']);

        // not processed
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
