<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class DefaultContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if (GeneralUtility::isEmpty($result)) {
            $default = $this->resolveContent($this->configuration, $this->context);
            if ($default !== null) {
                $result = $default;
            }
        }
        return false;
    }

    public function getWeight(): int
    {
        return 100;
    }
}
