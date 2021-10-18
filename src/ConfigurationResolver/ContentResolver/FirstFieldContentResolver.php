<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class FirstFieldContentResolver extends FieldContentResolver
{
    public function build()
    {
        $result = null;
        $fieldNames = GeneralUtility::castValueToArray($this->resolveContent($this->configuration));
        foreach ($fieldNames as $fieldName) {
            $value = $this->getFieldValue($fieldName);
            if ($value === null) {
                continue;
            }
            $result = $value;
            if (!GeneralUtility::isEmpty($result)) {
                break;
            }
        }
        return $result;
    }
}
