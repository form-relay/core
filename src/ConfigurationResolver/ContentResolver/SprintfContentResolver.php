<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

class SprintfContentResolver extends AbstractModifierContentResolver
{

    protected function modify(&$result)
    {
        if ($result instanceof MultiValueField) {
            $values = $result->toArray();
        } elseif (is_array($result)) {
            $values = $result;
        } else {
            $values = [$result];
        }
        $format = (string)$this->resolveContent($this->configuration);
        $result = sprintf($format, ...$values);
    }
}
