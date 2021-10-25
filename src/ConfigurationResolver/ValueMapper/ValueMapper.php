<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolver;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Form\MultiValueField;

abstract class ValueMapper extends ConfigurationResolver implements ValueMapperInterface
{
    protected static function getResolverInterface(): string
    {
        return ValueMapperInterface::class;
    }

    protected function resolveValue($fieldValue)
    {
        return $fieldValue;
    }

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    public function resolve($fieldValue = null)
    {
        if ($fieldValue === null) {
            $fieldValue = $this->getSelectedValue();
        }

        if ($fieldValue === null) {
            return null;
        }

        if ($fieldValue instanceof MultiValueField) {
            $result = [];
            foreach ($fieldValue as $key => $value) {
                $result[$key] = $this->resolve($value);
            }
            $class = get_class($fieldValue);
            return new $class($result);
        }
        return $this->resolveValue($fieldValue);
    }
}
