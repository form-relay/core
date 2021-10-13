<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class TrimContentResolver extends AbstractModifierContentResolver
{
    protected function modify(&$result)
    {
        $result = trim($result);
    }
}
