<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

class SprintfContentResolver extends AbstractModifierContentResolver
{
    protected function enabled(): bool
    {
        if ($this->configuration === false) {
            return false;
        }

        $format = $this->resolveContent($this->configuration);
        if ($format === null) {
            return false;
        }

        return true;
    }

    protected function modify(&$result)
    {
        if ($result instanceof MultiValueField) {
            $values = $result->toArray();
        } else {
            $values = [$result];
        }
        $result = sprintf($this->configuration, ...$values);
    }
}
