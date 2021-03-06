<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class TrimContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($this->config && $result !== null) {
            $result = trim($result);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 20;
    }
}
