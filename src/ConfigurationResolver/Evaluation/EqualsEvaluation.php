<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class EqualsEvaluation extends AbstractComparisonEvaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        return $this->compare(
            $this->getSelectedValue(),
            $this->resolveContent($this->configuration)
        );
    }
}
