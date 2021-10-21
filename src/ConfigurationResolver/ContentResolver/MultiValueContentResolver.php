<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

class MultiValueContentResolver extends AbstractWrapperContentResolver
{
    protected function getInitialValue()
    {
        return new MultiValueField([]);
    }

    protected function getSubContentResolver($key, $value)
    {
        return parent::getSubContentResolver('general', $value);
    }

    protected function add(&$result, $content, $key): bool
    {
        if ($content !== null) {
            $result[$key] = $content;
        }
        return true;
    }
}
