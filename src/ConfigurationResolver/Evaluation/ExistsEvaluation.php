<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class ExistsEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // exists
        $result = $this->fieldExists($this->getKeyFromContext());

        // does not exist
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
