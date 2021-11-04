<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

/** @deprecated  */
class NegateValueMapper extends ValueMapper
{
    const KEY_TRUE = 'true';
    const DEFAULT_TRUE = '1';

    const KEY_FALSE = 'false';
    const DEFAULT_FALSE = '0';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_IGNORE_SCALAR;
    }

    public function resolveValue($fieldValue): string
    {
        $config = $this->configuration;

        $true = static::DEFAULT_TRUE;
        if (isset($config[static::KEY_TRUE])) {
            $true = $config[static::KEY_TRUE];
            unset($config[static::KEY_TRUE]);
        }

        $false = static::DEFAULT_FALSE;
        if (isset($config[static::KEY_FALSE])) {
            $false = $config[static::KEY_FALSE];
            unset($config[static::KEY_FALSE]);
        }

        /** @var GeneralValueMapper $valueMapper */
        $valueMapper = $this->resolveKeyword('general', $config);
        $result = $valueMapper->resolve($fieldValue);

        return (bool)$result ? $false : $true;
    }
}
