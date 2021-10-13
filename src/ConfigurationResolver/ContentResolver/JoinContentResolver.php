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
            $glue = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_GLUE));
            $result->setGlue($glue);
            $result = (string)$result;
        }
        return false;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEYWORD_GLUE => static::DEFAULT_GLUE,
        ];
    }
}
