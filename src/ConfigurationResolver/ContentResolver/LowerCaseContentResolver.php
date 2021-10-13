<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class LowerCaseContentResolver extends AbstractModifierContentResolver
{
    protected function modify(&$result)
    {
        $result = strtolower($result);
    }
}
