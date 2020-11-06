<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\Utility\GeneralUtility;

class AppendKeyValueFieldMapper extends FieldMapper
{
    const KEY_SEPARATOR = 'separator';
    const DEFAULT_SEPARATOR = '\\n';

    const KEY_KEY_VALUE_SEPARATOR = 'keyValueSeparator';
    const DEFAULT_KEY_VALUE_SEPARATOR = '\\s=\\s';

    protected function ignoreScalarConfig()
    {
        return true;
    }

    public function finish(array &$result): bool
    {
        $keyValueSeparator = GeneralUtility::parseSeparatorString($this->config[static::KEY_KEY_VALUE_SEPARATOR] ?: static::DEFAULT_KEY_VALUE_SEPARATOR);
        $separator = GeneralUtility::parseSeparatorString($this->config[static::KEY_SEPARATOR] ?: static::DEFAULT_SEPARATOR);
        if (!isset($result[$this->context['mappedKey']])) {
            $result[$this->context['mappedKey']] = '';
        }
        $result[$this->context['mappedKey']] .= $this->context['key'] . $keyValueSeparator . $this->context['value'] . $separator;
        return true;
    }
}
