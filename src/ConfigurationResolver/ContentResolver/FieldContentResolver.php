<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class FieldContentResolver extends ContentResolver
{
    public function build(): string
    {
        return $this->getFieldValue($this->config);
    }
}
