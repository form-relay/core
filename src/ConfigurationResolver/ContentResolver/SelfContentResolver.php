<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class SelfContentResolver extends ContentResolver
{
    public function build()
    {
        return $this->configuration;
    }

    public function getWeight(): int
    {
        return 0;
    }
}
