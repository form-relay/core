<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

class NegateContentResolver extends ContentResolver
{
    const KEY_TRUE = 'true';
    const DEFAULT_TRUE = '1';

    const KEY_FALSE = 'false';
    const DEFAULT_FALSE = '0';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_IGNORE_SCALAR;
    }

    public function finish(&$result): bool
    {
        if ($result !== null) {
            $true = $this->resolveContent($this->getConfig(static::KEY_TRUE));
            $false = $this->resolveContent($this->getConfig(static::KEY_FALSE));
            if ($result === $true) {
                $result = $false;
            } elseif ($result === $false) {
                $result = $true;
            } else {
                $result = !!$result ? $false : $true;
            }
        }
        return false;
    }

    public function getWeight(): int
    {
        return 101;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_TRUE => static::DEFAULT_TRUE,
            static::KEY_FALSE => static::DEFAULT_FALSE,
        ];
    }
}
