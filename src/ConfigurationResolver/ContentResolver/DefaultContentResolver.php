<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class DefaultContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if (GeneralUtility::isEmpty($result)) {
            $result = $this->resolveContent($this->configuration, $this->context);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 100;
    }
}
