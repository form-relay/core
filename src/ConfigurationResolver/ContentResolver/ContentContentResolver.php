<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class ContentContentResolver extends ContentResolver
{
    public function build(): string
    {
        return $this->config;
    }

    public function getWeight(): int
    {
        return 0;
    }
}
