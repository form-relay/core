<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class InsertDataContentResolver extends AbstractModifierContentResolver
{
    protected function modifyValue(&$result)
    {
        $result = GeneralUtility::parseSeparatorString($result);
        $matches = [];
        if (preg_match('/^\\{([^\\}]+)\\}$/', $result, $matches)) {
            $result = $this->getFieldValue($matches[1]);
        } else {
            foreach (array_keys($this->context->getData()->toArray()) as $key) {
                if (strpos($result, '{' . $key . '}') !== false) {
                    $result = str_replace('{' . $key . '}', $this->getFieldValue($key), $result);
                }
            }
            $result = preg_replace('/\\{[-_a-zA-Z0-9]+\\}/', '', $result);
        }
    }
}
