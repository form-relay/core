<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class IfContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        $evalResult = $this->resolveEvaluation($this->configuration);
        if ($evalResult !== null) {
            $result = $this->resolveContent($evalResult);
            return true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return -1;
    }
}
