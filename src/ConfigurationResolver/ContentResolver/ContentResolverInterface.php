<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;

interface ContentResolverInterface extends ConfigurationResolverInterface
{
    const RESOLVER_TYPE = 'ContentResolver';

    public function build(): string;
    public function finish(string &$result): bool;
}
