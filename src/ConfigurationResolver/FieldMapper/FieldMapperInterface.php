<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;

interface FieldMapperInterface extends ConfigurationResolverInterface
{
    const RESOLVER_TYPE = 'FieldMapper';

    public function prepare(array &$result);
    public function finish(array &$result): bool;
}
