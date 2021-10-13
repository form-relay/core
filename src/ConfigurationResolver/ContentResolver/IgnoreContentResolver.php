<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class IgnoreContentResolver extends ContentResolver
{
    protected function ignore($result): bool
    {
        return (bool)$this->configuration;
    }

    public function finish(&$result): bool
    {
        if ($this->ignore($result)) {
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
