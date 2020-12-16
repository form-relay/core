<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Utility\GeneralUtility;

class JoinContentResolver extends ContentResolver
{
    const KEY_GLUE = 'glue';
    const DEFAULT_GLUE = '\\n';

    public function finish(&$result): bool
    {
        if ($result instanceof MultiValueField) {
            $glue = GeneralUtility::parseSeparatorString($this->config[static::KEY_GLUE] ?? static::DEFAULT_GLUE);
            $result = $result->__toString($glue);
        }
        return false;
    }
}
