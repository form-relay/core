<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class IgnoreFieldMapper extends FieldMapper
{
    public function finish(array &$result): bool
    {
        return !!$this->config;
    }
}
