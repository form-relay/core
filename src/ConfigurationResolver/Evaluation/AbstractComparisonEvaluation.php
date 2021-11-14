<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class AbstractComparisonEvaluation extends Evaluation
{
    protected function modifyValue($fieldValue)
    {
        $modifierConfig = $this->context['modifier'] ?? null;
        if ($modifierConfig) {
            $modifierConfig[SubmissionConfigurationInterface::KEY_SELF] = $fieldValue;
            $fieldValue = $this->resolveContent($modifierConfig);
        }
        return $fieldValue;
    }

    private function compareValue($fieldValue, $compareValue): bool
    {
        $fieldValue = $this->modifyValue($fieldValue);
        return (string)$fieldValue === (string)$compareValue;
    }

    private function compareLists($fieldValue, $compareList, bool $strict = false): bool
    {
        $fieldValue = GeneralUtility::castValueToArray($fieldValue);
        $compareList = GeneralUtility::castValueToArray($compareList);

        if (!$strict) {
            sort($fieldValue);
            sort($compareList);
        }

        return $fieldValue === $compareList;
    }

    protected function compare($fieldValue, $compareValue): bool
    {
        if (GeneralUtility::isList($fieldValue) || GeneralUtility::isList($compareValue)) {
            return $this->compareLists($fieldValue, $compareValue);
        }
        return $this->compareValue($fieldValue, $compareValue);
    }

    protected function findInList($fieldValue, array $list)
    {
        $fieldValue = $this->modifyValue($fieldValue);
        return array_search($fieldValue, $list);
    }

    protected function isInList($fieldValue, array $list): bool
    {
        $fieldValue = $this->modifyValue($fieldValue);
        return in_array($fieldValue, $list);
    }
}
