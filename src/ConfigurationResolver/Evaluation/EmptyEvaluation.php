<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Utility\GeneralUtility;

class EmptyEvaluation extends AbstractIsEvaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return GeneralUtility::isEmpty($fieldValue);
    }
}
