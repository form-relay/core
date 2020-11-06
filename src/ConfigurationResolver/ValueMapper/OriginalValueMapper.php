<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

class OriginalValueMapper extends ValueMapper
{
    public function resolveValue($fieldValue)
    {
        if ($this->config) {
            return $fieldValue;
        }
        return null;
    }
}
