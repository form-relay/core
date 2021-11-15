<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class FirstOfContentResolver extends ContentResolver
{
    public function build()
    {
        ksort($this->configuration, SORT_NUMERIC);
        $result = null;
        foreach ($this->configuration as $key => $valueConfiguration) {
            $value = $this->resolveContent($valueConfiguration);
            if ($value !== null) {
                $result = $value;
            }
            if (!GeneralUtility::isEmpty($result)) {
                break;
            }
        }
        return $result;
    }
}
