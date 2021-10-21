<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;

class DiscreteMultiValueContentResolver extends MultiValueContentResolver
{
    protected function getInitialValue(): MultiValueField
    {
        return new DiscreteMultiValueField([]);
    }
}
