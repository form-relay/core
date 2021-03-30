<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class MapContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($result !== null) {
            $result = $this->resolveValueMap($this->configuration, $result);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 100;
    }
}
