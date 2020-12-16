<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

class IfContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        $evalResult = $this->resolveEvaluation($this->config);
        if ($evalResult !== null) {
            /** @var GeneralContentResolver $contentResolver */
            $contentResolver = $this->resolveKeyword('general', $evalResult);
            $result = $contentResolver->resolve();
            return true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return -1;
    }
}
