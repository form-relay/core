<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

class SprintfContentResolver extends AbstractModifierContentResolver
{

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
