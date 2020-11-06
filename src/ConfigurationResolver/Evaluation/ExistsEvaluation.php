<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class ExistsEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        // does not exist
        $result = !isset($context['data'][$context['key']]);

        // exists
        if ($this->config) {
            $result = !$result;
        }

        return $result;
    }
}
