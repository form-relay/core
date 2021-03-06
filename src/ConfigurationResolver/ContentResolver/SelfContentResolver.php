<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class SelfContentResolver extends FieldContentResolver
{
    public function build()
    {
        return $this->config;
    }

    public function getWeight(): int
    {
        return 0;
    }
}
