<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;

interface ValueMapperInterface extends ConfigurationResolverInterface
{
    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    public function resolve($fieldValue = null);
}
