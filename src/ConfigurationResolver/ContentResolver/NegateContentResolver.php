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
        if ($result === $true) {
            $result = $false;
        } elseif ($result === $false) {
            $result = $true;
        } else {
            $result = !!$result ? $false : $true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return 101;
    }
}
