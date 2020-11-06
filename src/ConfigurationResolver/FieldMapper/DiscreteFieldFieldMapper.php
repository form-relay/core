<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;

class DiscreteFieldFieldMapper extends FieldMapper
{
    protected function finishValue($fieldValue, &$result)
    {
        $result[$this->context['mappedKey']]->append($fieldValue);
    }

    public function finish(array &$result): bool
    {
        if (!isset($result[$this->context['mappedKey']])) {
            // if not set yet, create a discrete multi-value field
            $result[$this->context['mappedKey']] = new DiscreteMultiValueField([]);
        } elseif ($result[$this->context['mappedKey']] instanceof MultiValueField) {
            // if already a multi-value field, transfer to a discrete one
            $values = $result[$this->context['mappedKey']];
            $result[$this->context['mappedKey']] = new DiscreteMultiValueField([]);
            foreach ($values as $value) {
                $result[$this->context['mappedKey']]->append($value);
            }
        } else {
            // if already set with some other value, insert it to a new discrete multi-value field
            $result[$this->context['mappedKey']] = new DiscreteMultiValueField([$result[$this->context['mappedKey']]]);
        }

        parent::finish($result);
        return true;
    }
}
