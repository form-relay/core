<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class ExistsEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // exists
        $exists = $this->fieldExists($this->context['key']);

        // does not exist
        if (!$this->config) {
            $result = !$result;
        }

        return $result;
    }
}
