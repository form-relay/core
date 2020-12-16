<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class NegateContentResolver extends ContentResolver
{
    const KEY_TRUE = 'true';
    const DEFAULT_TRUE = '1';

    const KEY_FALSE = 'false';
    const DEFAULT_FALSE = '0';

    protected function ignoreScalarConfig()
    {
        return true;
    }

    public function finish(&$result): bool
    {
        $true = $this->config[static::KEY_TRUE] ?? static::DEFAULT_TRUE;
        $false = $this->config[static::KEY_FALSE] ?? static::DEFAULT_FALSE;
        return !!$result ? $false : $true;
    }

    public function getWeight(): int
    {
        return 101;
    }
}
