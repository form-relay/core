<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Form\MultiValueField;

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
            if (!$this->fieldExists($requiredField)) {
                $result = false;
                break;
            }
            $value = $this->getFieldValue($requiredField);
            if (!$value) {
                $result = false;
                break;
            }
            if (
                $value instanceof MultiValueField
                && count($value) === 0
            ) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}
