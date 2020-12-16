<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

class IgnoreIfEmptyContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($this->config && ($result === '' || $result === null)) {
            $result = null;
            return true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return 102;
    }
}
