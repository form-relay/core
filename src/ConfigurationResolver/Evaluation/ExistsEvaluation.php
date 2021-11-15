<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class ExistsEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // exists
        $key = $this->getKeyFromContext();
        $result = $key && $this->fieldExists($key);

        // does not exist
        if (!$this->configuration) {
            $result = !$result;
        }

        return $result;
    }
}
