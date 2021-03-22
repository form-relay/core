<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;

class MultiValueContentResolver extends ContentResolver
{
    protected function getMultiValueField(): MultiValueField
    {
        return new MultiValueField([]);
    }

    public function build()
    {
        $result = $this->getMultiValueField();
        foreach ($this->config as $key => $valueConfiguration) {
            $value = $this->resolveContent($valueConfiguration);
            if ($value !== null) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
