<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class DefaultContentResolver extends ContentResolver
{
    public function finish(&$result): bool
    {
        if ($result === '' || $result === null) {
            /** @var GeneralContentResolver $contentResolver */
            $contentResolver = $this->resolveKeyword('general', $this->config, $this->context);
            $result = $contentResolver->resolve();
        }
        return false;
    }

    public function getWeight(): int
    {
        return 100;
    }
}
