<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

class SelfValueMapper extends ValueMapper
{
    public function resolveValue($fieldValue): string
    {
        return $this->configuration;
    }

    public function getWeight(): int
    {
        return 20;
    }
}
