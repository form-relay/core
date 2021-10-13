<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class RawContentResolver extends ContentResolver
{
    public function build()
    {
        return $this->configuration;
    }
}
