<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class RequiredEvaluation extends Evaluation
{
    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_EXPLODE;
    }

    public function eval(array $keysEvaluated = []): bool
    {
        $result = true;
        foreach ($this->configuration as $requiredField) {
            if (GeneralUtility::isEmpty($this->getFieldValue($requiredField))) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}
