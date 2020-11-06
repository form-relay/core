<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class IfEmptyFieldMapper extends FieldMapper
{
    public function finish(array &$result): bool
    {
        if (isset($result[$this->context['mappedKey']])) {
            return true;
        }
        return false;
    }
}
