<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Form\MultiValueField;

class EqualsEvaluation extends AbstractComparisonEvaluation
{
    public function eval(array $keysEvaluated = []): bool
    {
        return $this->compare(
            $this->getSelectedValue(),
            $this->resolveContent($this->configuration)
        );
    }

    protected function evalMultiValue(MultiValueField $fieldValue, array $keysEvaluated = []): bool
    {
        return $this->compare($fieldValue, $this->resolveContent($this->configuration));
    }
}
