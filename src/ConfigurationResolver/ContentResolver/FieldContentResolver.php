<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class FieldContentResolver extends ContentResolver
{
    public function build()
    {
        /** @var GeneralContentResolver $contentResolver */
        $contentResolver = $this->resolveKeyword('general', $this->config);
        $fieldName = $contentResolver->resolve();
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
