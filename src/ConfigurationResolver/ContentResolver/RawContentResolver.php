<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

/** @deprecated */
class RawContentResolver extends ContentResolver
{
    public function build()
    {
        return $this->configuration;
    }
}
