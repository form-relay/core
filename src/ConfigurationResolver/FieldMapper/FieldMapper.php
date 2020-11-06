<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolver;
use FormRelay\Core\Model\Form\MultiValueField;

abstract class FieldMapper extends ConfigurationResolver implements FieldMapperInterface
{
    protected static function getResolverInterface(): string
    {
        return FieldMapperInterface::class;
    }

    protected function prepareValue($fieldValue, array &$result)
    {
        return $fieldValue;
    }

    public function prepare(array &$result)
    {
        $fieldValue = $this->context['value'];
        if ($fieldValue instanceof MultiValueField) {
            $multiValue = [];
            foreach ($fieldValue as $value) {
                $multiValue[] = $this->prepareValue($value, $multiValue);
            }
            $class = get_class($fieldValue);
            $this->context['value'] = new $class($multiValue);
        } else {
            $this->context['value'] = $this->prepareValue($fieldValue, $result);
        }
    }

    protected function finishValue($fieldValue, &$result)
    {
    }

    public function finish(array &$result): bool
    {
        $fieldValue = $this->context['value'];
        if ($fieldValue instanceof MultiValueField) {
            foreach ($fieldValue as $value) {
                $this->finishValue($value, $result);
            }
        } else {
            $this->finishValue($fieldValue, $result);
        }
        return false;
    }
}
