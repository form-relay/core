<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\Model\Form\FieldInterface;

interface ContentResolverInterface extends ConfigurationResolverInterface
{
    const RESOLVER_TYPE = 'ContentResolver';

    /**
     * @return null|string|FieldInterface
     */
    public function build();

    /**
     * @param null|string|FieldInterface $result
     * @return bool
     */
    public function finish(&$result): bool;
}
