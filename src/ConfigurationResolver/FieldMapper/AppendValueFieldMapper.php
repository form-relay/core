<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\Utility\GeneralUtility;

class AppendValueFieldMapper extends FieldMapper
{
    const KEY_SEPARATOR = 'separator';
    const DEFAULT_SEPARATOR = '\\n';

    protected function ignoreScalarConfig()
    {
        return true;
    }

    public function finish(array &$result): bool
    {
        $separator = GeneralUtility::parseSeparatorString($this->config[static::KEY_SEPARATOR] ?? static::DEFAULT_SEPARATOR);
        if (!isset($result[$this->context['mappedKey']])) {
            $result[$this->context['mappedKey']] = '';
        }
        $result[$this->context['mappedKey']] .= $this->context['value'] . $separator;
        return true;
    }
}
