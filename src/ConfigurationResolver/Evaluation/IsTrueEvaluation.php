<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Utility\GeneralUtility;

class IsTrueEvaluation extends AbstractIsEvaluation
{
    protected function evalMultiValue(MultiValueField $fieldValue, array $keysEvaluated = []): bool
    {
        return $this->evalValue($fieldValue, $keysEvaluated);
    }

    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return GeneralUtility::isTrue($fieldValue);
    }
}
