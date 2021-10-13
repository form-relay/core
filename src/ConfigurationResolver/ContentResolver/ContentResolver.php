<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolver;

abstract class ContentResolver extends ConfigurationResolver implements ContentResolverInterface
{
    const KEYWORD_GLUE = 'glue';

    protected static function getResolverInterface(): string
    {
        return ContentResolverInterface::class;
    }

    public function build()
    {
        return null;
    }

    public function finish(&$result): bool
    {
        return false;
    }
}
