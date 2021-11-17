<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;

interface ContentResolverInterface extends ConfigurationResolverInterface
{
    /**
     * @return string|FieldInterface|null
     */
    public function build();

    /**
     * @param string|FieldInterface|null $result
     * @return bool
     */
    public function finish(&$result): bool;
}
