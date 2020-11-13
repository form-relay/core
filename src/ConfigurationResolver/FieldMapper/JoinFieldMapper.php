<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Utility\GeneralUtility;

class JoinFieldMapper extends FieldMapper
{
    const KEY_GLUE = 'glue';
    const DEFAULT_GLUE = '\\n';

    public function prepare(array &$result)
    {
        if ($this->context['value'] instanceof MultiValueField) {
            $glue = GeneralUtility::parseSeparatorString($this->config[static::KEY_GLUE] ?? static::DEFAULT_GLUE);
            $this->context['value'] = $this->context['value']->__toString($glue);
        }
    }
}
