<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class TrimContentResolver extends ContentResolver
{
    public function finish(string &$result): bool
    {
        if ($this->config) {
            $result = trim($result);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 20;
    }
}
