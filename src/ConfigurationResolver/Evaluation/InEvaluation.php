<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class InEvaluation extends Evaluation
{
    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_RESOLVE_CONTENT_THEN_CAST_TO_ARRAY;
    }

    public function eval(array $keysEvaluated = []): bool
    {
        return GeneralUtility::isInList($this->getSelectedValue(), $this->configuration);
    }
}
