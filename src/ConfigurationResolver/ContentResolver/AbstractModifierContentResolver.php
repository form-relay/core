<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

abstract class AbstractModifierContentResolver extends ContentResolver
{
    abstract protected function modify(&$result);

    public function finish(&$result): bool
    {
        if ($this->configuration && $result !== null) {
            $this->modify($result);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 20;
    }
}
