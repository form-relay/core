<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class ContentFieldMapper extends FieldMapper
{
    public function prepare(array &$result)
    {
        $this->context['mappedKey'] = $this->config;
    }

    public function finish(array &$result): bool
    {
        $result[$this->context['mappedKey']] = $this->context['value'];
        return true;
    }

    public function getWeight(): int
    {
        return 20;
    }
}
