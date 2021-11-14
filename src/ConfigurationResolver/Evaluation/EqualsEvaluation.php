<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class EqualsEvaluation extends Evaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        return GeneralUtility::compare(
            $this->getSelectedValue(),
            $this->resolveContent($this->configuration)
        );
    }
}
