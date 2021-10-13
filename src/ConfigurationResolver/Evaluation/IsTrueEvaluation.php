<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class IsTrueEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return GeneralUtility::isTrue($fieldValue);
    }
}
