<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class IsFalseEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return GeneralUtility::isFalse($fieldValue);
    }
}
