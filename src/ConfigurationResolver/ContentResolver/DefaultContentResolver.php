<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class DefaultContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($result === '' || $result === null) {
            $result = $this->resolveContent($this->config, $this->context);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 100;
    }
}
