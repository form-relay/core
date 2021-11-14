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
