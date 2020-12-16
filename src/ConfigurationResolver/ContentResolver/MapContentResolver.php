<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;

class MapContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($result !== null) {
            $result = $this->resolveValueMap($this->config, $result);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 100;
    }
}
