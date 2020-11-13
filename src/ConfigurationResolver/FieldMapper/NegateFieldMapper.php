<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class NegateFieldMapper extends FieldMapper
{
    const KEY_TRUE = 'true';
    const DEFAULT_TRUE = '1';

    const KEY_FALSE = 'false';
    const DEFAULT_FALSE = '0';

    protected function ignoreScalarConfig()
    {
        return true;
    }

    protected function prepareValue($fieldValue, array &$result)
    {
        $true = $this->config[static::KEY_TRUE] ?? static::DEFAULT_TRUE;
        $false = $this->config[static::KEY_FALSE] ?? static::DEFAULT_FALSE;
        return !!$fieldValue ? $false : $true;
    }
}
