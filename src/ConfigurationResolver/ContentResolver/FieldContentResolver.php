<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class FieldContentResolver extends ContentResolver
{
    public function build()
    {
        $fieldName = $this->resolveContent($this->configuration);
        if ($fieldName) {
            return $this->getFieldValue($fieldName);
        }
        return '';
    }

    public function getWeight(): int
    {
        return 0;
    }
}
