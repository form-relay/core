<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class InsertDataContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($this->config && $result !== null) {
            $result = GeneralUtility::parseSeparatorString($result);
            $matches = [];
            if (preg_match('/^\\{([^\\}]+)\\}$/', $result, $matches)) {
                $result = $this->getFieldValue($matches[1]);
            } else {
                foreach (array_keys(iterator_to_array($this->context['data'])) as $key) {
                    if (strpos($result, '{' . $key . '}') !== false) {
                        $result = str_replace('{' . $key . '}', $this->getFieldValue($key), $result);
                    }
                }
                $result = preg_replace('/\\{[-_a-zA-Z0-9]+\\}/', '', $result);
            }
        }
        return false;
    }
}
