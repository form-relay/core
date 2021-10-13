<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class IgnoreIfEmptyContentResolver extends IgnoreContentResolver
{
    protected function ignore($result): bool
    {
        return parent::ignore($result) && GeneralUtility::isEmpty($result);
    }

    public function getWeight(): int
    {
        return 102;
    }
}
