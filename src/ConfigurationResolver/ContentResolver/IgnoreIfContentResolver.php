<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

class IgnoreIfContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($this->evaluate($this->config)) {
            $result = null;
            return true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return 0;
    }
}
