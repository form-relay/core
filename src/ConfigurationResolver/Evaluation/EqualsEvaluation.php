<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Form\MultiValueField;

class EqualsEvaluation extends AbstractComparisonEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return $this->compare($fieldValue, $this->resolveContent($this->configuration));
    }

    protected function evalMultiValue(MultiValueField $fieldValue, array $keysEvaluated = []): bool
    {
        return $this->compare($fieldValue, $this->resolveContent($this->configuration));
    }
}
