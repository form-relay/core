<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class PassthroughFieldMapper extends FieldMapper
{
    public function finish(array &$result): bool
    {
        $result[$this->context['key']] = $this->context['value'];
        return true;
    }
}
