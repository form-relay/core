<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Utility\GeneralUtility;

class GeneralContentResolver extends AbstractWrapperContentResolver implements GeneralConfigurationResolverInterface
{
    protected $glue = '';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    protected function getGlue(): string
    {
        return $this->glue;
    }

    protected function preprocessConfiguration(): array
    {
        $config = $this->configuration;
        if (array_key_exists(static::KEYWORD_GLUE, $config)) {
            $glue = $this->resolveContent($config[static::KEYWORD_GLUE]);
            $this->glue = GeneralUtility::parseSeparatorString($glue);
            unset($config[static::KEYWORD_GLUE]);
        }
        return $config;
    }

    /**
     * @return FieldInterface|string|null
     */
    public function resolve()
    {
        $result = $this->build();
        $this->finish($result);
        return $result;
    }
}
