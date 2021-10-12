<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class UpperCaseContentResolver extends AbstractModifierContentResolver
{
    protected function modify(&$result)
    {
        $result = strtoupper($result);
    }
}
