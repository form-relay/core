<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

abstract class AbstractModifierContentResolver extends ContentResolver
{
    protected function modifyValue(&$result)
    {
    }

    protected function modify(&$result)
    {
        if ($result instanceof MultiValueField) {
            foreach ($result as $key => $value) {
                $this->modify($result[$key]);
            }
        } else {
            $this->modifyValue($result);
        }
    }

    protected function enabled(): bool
    {
        $result = $this->resolveContent($this->configuration);
        if ($result instanceof MultiValueField) {
            $result = $result->toArray();
        }
        return (bool)$result;
    }

    public function finish(&$result): bool
    {
        if ($this->enabled() && $result !== null) {
            $this->modify($result);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 20;
    }
}
