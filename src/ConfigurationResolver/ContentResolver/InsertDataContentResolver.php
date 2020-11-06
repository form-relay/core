<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class InsertDataContentResolver extends ContentResolver
{
    public function finish(string &$result): bool
    {
        if ($this->config) {
            $result = GeneralUtility::parseSeparatorString($result);
            foreach (array_keys($this->context['data']) as $key) {
                $result = str_replace('{' . $key . '}', $this->getFieldValue($key), $result);
            }
            $result = preg_replace('/\\{[-_a-zA-Z0-9]+\\}/', '', $result);
        }
        return false;
    }
}
